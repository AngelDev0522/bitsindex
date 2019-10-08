<template>
    <div class="user-log">
        <input type="text" class="form-control" placeholder="Search" v-model="search">
        <div v-for="user in users" :user="user" v-bind:key="user.id" @click="activateUser(user)" :id="user.id">
            <div v-if='user.name.includes(search)' class="chat-user">
                <span v-if="user.online == 1" class="pull-right label label-primary">Online</span>
                <img v-bind:src="'/storage/'+user.avatar" class="chat-avatar">
                <div class="chat-user-name">
                    {{ user.name}}
                </div>
            </div>
        </div>
        <li v-show="users.length === 0" disabled>No friends found</li>
    </div>
</template>

<script>
    export default {
        // props: ['user', 'user2'],
        methods:{
            listen() {
                Echo.join('chat')
                    .joining((user) => {
                        console.log('joining ');
                        axios.get('/user/'+ user.id +'/online');
                    })
                    .leaving((user) => {
                        console.log('leaving ');
                        axios.get('/user/'+ user.id +'/offline');
                    })
                    .listen('UserOnline', (e) => {
                        console.log('useronline ', e.user);
                        this.friend = e.user;
                    })
                    .listen('UserOffline', (e) => {
                        console.log('useroffline ', e.user);
                        this.friend = e.user;
                    });
            },
            activateUser (selectedUser) {
                this.$emit('getcurrentuser',{
                    userId:selectedUser.id
                });
                // show chat conversation
                $(".activate-chat").show();
                $(".chat-info").hide();
                // to make clicked li active
                $(".user-log .active").removeClass("active");
                $("#"+selectedUser.id).addClass("active");
            },
        },
        data() {
            return {
                search: '',
                default_image:$("#default_image").val(),
                users:[],
                url:$("#base_url").val(),
                friend: this.user2
            }
        },
        mounted() {
            // get users lists in left sidebar
            axios.get('/users').then( response=> {
                this.users = response.data;
            });
            this.listen();
        }
    }
</script>
