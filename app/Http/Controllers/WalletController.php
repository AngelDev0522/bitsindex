<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Auth;
use Validator;
use Session;
use GuzzleHttp\Client;
use GuzzleHttp;

class WalletController extends Controller
{
    public function __construct()
    {
    }

    public function litecoin(Request $request){
        $user = Auth::user();
        return $this->renderWalletView($user, [
            'coinOfficialName' => 'Litecoin',
            'coinName' => 'litecoin',
            'coinUnit' => 'LTC',
            'coinAddress' => 'litecoin_address',
        ]);
    }

    public function ripple(Request $request){
        $user = Auth::user();
        return $this->renderWalletView($user, [
            'coinOfficialName' => 'Ripple',
            'coinName' => 'ripple',
            'coinUnit' => 'XRP',
            'coinAddress' => 'ripple_address',
        ]);
    }

    public function peercoin(Request $request){
        $user = Auth::user();
        return $this->renderWalletView($user, [
            'coinOfficialName' => 'Peercoin',
            'coinName' => 'peercoin',
            'coinUnit' => 'PPC',
            'coinAddress' => 'peercoin_address',
        ]);
    }

    public function renderWalletView($user, $coinInfo){
        $nativeBalance = $usdBalance = $rate = 0;
        $coinAddress = $user[$coinInfo['coinAddress']];
        // $coinAddress = 'PEuX3MMxDSnTenfSQQfsc1j24JJ93cYuwq';  // PEuX3MMxDSnTenfSQQfsc1j24JJ93cYuwq much PPC
        $result = json_decode(file_get_contents("http://localhost:8080/api/v1/{$coinInfo['coinName']}/balance/$coinAddress"), true);
        // $result = ['success' => true, 'data' => 123];
        if($result['success'])
            $nativeBalance = $result['data'];
        $result = json_decode(file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms={$coinInfo['coinUnit']}&tsyms=USD"), true);// to array
        $rate = $result['RAW'][$coinInfo['coinUnit']]['USD']['PRICE'];
        // $rate = 123.543;
        $usdBalance = round($nativeBalance * $rate, 2);
        $result = json_decode(file_get_contents("http://localhost:8080/api/v1/{$coinInfo['coinName']}/history/$coinAddress"));
        $history = $result->success ? $result->data : [];
        // $history = [];

        return View('wallet.layout', $coinInfo + [ //array merge
            // 'user' => compact('user')['user'],
            'address' => $coinAddress,
            'nativeBalance' => $nativeBalance,
            'usdBalance' => $usdBalance,
            'rate' => $rate,
            'history' => $history,
        ]);
    }

    public static function genTxLink($hash, $coin, $net = 'main'){
        $coin = strtolower($coin);
        if($coin == 'ripple'){
            return "<a href='https://bithomp.com/explorer/$hash' target='_blank'>$hash</a>";
        }
        if($coin == 'litecoin'){
            return "<a href='https://insight.litecore.io/tx/$hash' target='_blank'>$hash</a>";
        }
        return "<a href='https://explorer.peercoin.net/tx/$hash' target='_blank'>$hash</a>";
    }

    public function coinSend(Request $request, $coin)
    {
        $coin_address = $coin.'_address';
        $coin_secret = $coin.'_secret';

        $validation = Validator::make($request->all(), [
            'receiver' => 'min:25|max:35|string|required',
            'amount' => 'numeric|required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()
                    ->withErrors($validation)
                    ->withInput();
        }

        try {
            $user = Auth::user();

            $receiver = $request->receiver;
            $amount = $request->amount;

            if( $user[$coin_address] == $receiver ){
                Session::flash('message', "Can't send to yourself!" );
                Session::flash('type', 'error');
                return redirect()->back();
            }

            $client = new Client();

            $response = $client->post("http://localhost:8080/api/v1/$coin/send", [
                GuzzleHttp\RequestOptions::JSON => [
                    'sender' => $user[$coin_address],
                    'receiver' => $receiver,
                    'amount' => $amount,
                    'secret' => $user[$coin_secret]
                ]
            ]);

            $body = $response->getBody()->getContents();
            $result = json_decode($body);

            if(!isset($result->success) || !$result->success){
                throw "result is false";
            }
        //code...
        } catch (\Throwable $th) {
            Session::flash('message', "Failed to send!" );
            Session::flash('type', 'error');
            return redirect()->back();
        }

        //sending okay

        $alertMessage = "Sent successfully with transaction ID "
                        .$this->genTxLink($result->data, $coin, 'main')
                        ."<br>Balance will be updated few minutes later.";

        Session::flash('alertMessage', $alertMessage );
        Session::flash('alertType', 'success');

        Session::flash('message', "Sent!" );
        Session::flash('type', 'success');
        return redirect()->back();
    }

    public function rippleSend(Request $request){
        return $this->coinSend($request, 'ripple');
    }

    public function litecoinSend(Request $request){
        return $this->coinSend($request, 'litecoin');
    }

    public function peercoinSend(Request $request){
        return $this->coinSend($request, 'peercoin');
    }

    private function _importSecret(Request $request, $coin){
        $validation = Validator::make($request->all(), [
            'password' => 'min:8|string|required',
            'private_key' => 'string|required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()
                    ->withErrors($validation);
        }

        $password = $request->password;
        $secret = $request->private_key;

        $user = Auth::user();
        if (!Hash::check($password, $user->password)) {
            // Wrong Password
            Session::flash('message', "Wrong Password!" );
            Session::flash('type', 'error');
            return redirect()->back()
                    ->withErrors(['password' => 'Password is wrong']);
        }

        $coinAddress = $coin.'_address';
        $coinSecret = $coin.'_secret';

        $json = file_get_contents("http://localhost:8080/api/v1/$coin/secret2address/$secret");
        $result = json_decode($json);

        if(!$result->success){
            Session::flash('message', "Invalid ".ucfirst($coin)." Private Key!" );
            Session::flash('type', 'error');
            return redirect()->back();
        }

        $user->$coinSecret = $secret;
        $user->$coinAddress = $result->data;
        $user->update();

        Session::flash('message', "Updated!" );
        Session::flash('type', 'success');
        return redirect()->back();
    }

    private function _exportSecret(Request $request, $coin){
        $validation = Validator::make($request->all(), [
            'password' => 'min:8|string|required',
        ]);

        if ($validation->fails()) {
            return redirect()->back()
                    ->withErrors($validation);
        }

        $user = Auth::user();
        if (!Hash::check($request->password, $user->password)) {
            Session::flash('message', "Wrong Password!" );
            Session::flash('type', 'error');
            // Wrong Password
            return redirect()->back();
                    // ->withErrors(['password' => 'Password is wrong']);
        }
        $coinAddress = $coin.'_address';
        $coinSecret = $coin.'_secret';

        $secret = $user->$coinSecret;

        $response = new StreamedResponse();
        $response->setCallBack(function () use($secret) {
            echo $secret;
        });
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $coin.'-private.key');
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'text/plain');
        // $response->headers->set('Refresh', 2);
        // $response->headers->set('location', 'index.php');
        // header("Refresh: $sec; url=$page");

        return $response;
    }

    public function litecoinImportSecret(Request $request){
        return $this->_importSecret($request, 'litecoin');
    }

    public function litecoinExportSecret(Request $request){
        return $this->_exportSecret($request, 'litecoin');
    }

    public function peercoinImportSecret(Request $request){
        return $this->_importSecret($request, 'peercoin');
    }

    public function peercoinExportSecret(Request $request){
        return $this->_exportSecret($request, 'peercoin');
    }

    public function rippleImportSecret(Request $request){
        return $this->_importSecret($request, 'ripple');
    }

    public function rippleExportSecret(Request $request){
        return $this->_exportSecret($request, 'ripple');
    }
}
