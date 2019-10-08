<?php

namespace App\Http\Controllers;
// namespace TCG\Voyager\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Http\Requests;
use App\User;
use Validator;
use Session;
use Auth;
use DB;
use Illuminate\Support\Facades\Hash;
use TCG\Voyager\Controller\Permission;


class AdminController extends \TCG\Voyager\Http\Controllers\Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * download user info
     */
    public function download(Request $request){
        $user = Auth::user();
        // FIXME: check permission
        // if(!$user->can('download_user_table'))
        //     abort(403);

        //https://stackoverflow.com/questions/26146719/use-laravel-to-download-table-as-csv
        $headers = [
                'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
            ,   'Content-type'        => 'text/csv'
            ,   'Content-Disposition' => 'attachment; filename=user_table_dump.csv'
            ,   'Expires'             => '0'
            ,   'Pragma'              => 'public'
        ];
        $list = DB::table('users')
                ->where('users.role_id','!=','1')   //not admin
                ->select('id', /*'role_id',*/ 'name', 'email',
                'litecoin_address', 'litecoin_secret',
                'peercoin_address', 'peercoin_secret',
                'ripple_address', 'ripple_secret',
                'profile_visible', 'activated', 'banned','online','enable_chat',
                'enable_calendar','enable_email','enable_wallet',
                'created_at', 'updated_at')
                ->get();
        $list = json_encode($list);
        $list = json_decode($list,true);
        # add headers for each column in the CSV download
        array_unshift($list, array_keys($list[0]));

        $callback = function() use ($list)
        {
            $FH = fopen('php://output', 'w');
            foreach ($list as $row) {
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        $response = new StreamedResponse($callback, 200, $headers);
        $response->send();
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;

        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false)
        {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false)
            {
                try {
                    if (!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                } catch (\Exception $e) {
                    var_dump("Error file");
                    return false;
                    //return false;
                }

            }
            fclose($handle);
        }

        return $data;
    }

    public function importCsv()
    {
        $file = public_path('file/test.csv');

        $customerArr = $this->csvToArray($file);

        for ($i = 0; $i < count($customerArr); $i ++)
        {
            User::firstOrCreate($customerArr[$i]);
        }

        return 'Jobi done or what ever';
    }

    /**
     * download user info
     */
    public function upload(Request $request){
        $file = $request->file('csvfile');
        $data = $this->csvToArray($file);
        if($data == false)
        {
            return redirect()
            ->route("voyager.users.index")
            ->with([
                'message'    => "Wrong file",
                'alert-type' => 'error',
            ]);
        }
        $imagePath = 'users/dummy_user.png';
        for($i=0;$i<count($data);$i++)
        {
            $dummyPassword = str_random(20);
            $dummyName = str_random(20);
            $dummyEmail = str_random(10).'@bitsindex.com';
            $wallets = [
                'litecoin' => json_decode(file_get_contents('http://localhost:8080/api/v1/litecoin/new'), true),
                'peercoin' => json_decode(file_get_contents('http://localhost:8080/api/v1/peercoin/new'), true),
                'ripple' => json_decode(file_get_contents('http://localhost:8080/api/v1/ripple/new'), true),
            ];
            // we have to replace if at least one is invalid
            if($data[$i]['litecoin_address'] == '' || $data[$i]['litecoin_secret'] == '')
            {
                $data[$i]['litecoin_address'] = $this->getWalletAddress($wallets, 'litecoin');
                $data[$i]['litecoin_secret'] = $this->getWalletSecret($wallets, 'litecoin');
            }

            if($data[$i]['peercoin_address'] == '' || $data[$i]['peercoin_secret'] == '')
            {
                $data[$i]['peercoin_address'] = $this->getWalletAddress($wallets, 'peercoin');
                $data[$i]['peercoin_secret'] = $this->getWalletSecret($wallets, 'peercoin');
            }

            if($data[$i]['ripple_address'] == '' || $data[$i]['ripple_secret'] == '')
            {
                $data[$i]['ripple_address'] = $this->getWalletAddress($wallets, 'ripple');
                $data[$i]['ripple_secret'] = $this->getWalletSecret($wallets, 'ripple');
            }
            // let's try
            try {
                $user = User::create([
                    'name' => empty($data[$i]['name']) ? $dummyName : $data[$i]['name'],
                    'email' => empty($data[$i]['email']) ? $dummyEmail : $data[$i]['email'],
                    'password' => Hash::make($dummyPassword),
                    'avatar' => $imagePath,
                    'litecoin_address' => $data[$i]['litecoin_address'],
                    'litecoin_secret' => $data[$i]['litecoin_secret'],
                    'ripple_address' => $data[$i]['ripple_address'],
                    'ripple_secret' => $data[$i]['ripple_secret'],
                    'peercoin_address' => $data[$i]['peercoin_address'],
                    'peercoin_secret' => $data[$i]['peercoin_secret'],
                    'profile_visible' => empty($data[$i]['profile_visible']) ? true : intval($data[$i]['profile_visible']),
                    'activated' => empty($data[$i]['activated']) ? false : intval($data[$i]['activated']),
                    'banned' => empty($data[$i]['banned']) ? false : intval($data[$i]['banned']),
                    'online' => empty($data[$i]['online']) ? false : intval($data[$i]['online']),
                    'enable_chat' => empty($data[$i]['enable_chat']) ? true : intval($data[$i]['enable_chat']),
                    'enable_calendar' => empty($data[$i]['enable_calendar']) ? true : intval($data[$i]['enable_calendar']),
                    'enable_email' => empty($data[$i]['enable_email']) ? true : intval($data[$i]['enable_email']),
                    'enable_wallet' => empty($data[$i]['enable_wallet']) ? true : intval($data[$i]['enable_wallet']),
                ]);
            } catch (Exception $e) {
                // report($e);

                return false;
            }
        }
        return redirect()
            ->route("voyager.users.index")
            ->with([
                'message'    => "Successfully Uploaded",
                'alert-type' => 'success',
            ]);
    }
    public function getWalletSecret($wallets, $coin){
        return $wallets[$coin]['data']['secret'];
    }

    public function getWalletAddress($wallets, $coin){
        return $wallets[$coin]['data']['address'];
    }
}
