<template>
    <div class="chat-message left">
        <img class="message-avatar" v-bind:src="'/storage/'+message.avatar" alt="" >
        <div class="message">
            <a class="message-author" href="#"> {{ message.user }} </a>
            <!-- <span class="message-date"> {{ formatTime(message.time) }} </span> -->
            <span v-if="message.type == 1" class="message-content">
                {{ message.message }}
            </span>
            <span v-else-if="message.type == 4">
                <a :href="message.file_path" download>
                    <img :src="message.file_path" style="max-width:500px">
                    <!-- {{ message.file_name }} -->
                </a>
            </span>
            <span v-else>
                <a :href="message.file_path" download>
                    {{ message.file_name }}
                </a>
            </span>
        </div>
    </div>
</template>

<script>
    export default {
        props:['message'],
        data() {
            return {
                userName:$("#user_name").val()
            }
        },
        methods:{
            formatTime (time) {
                let previousTime = moment(time,'YYYY-MM-DD HH:mm:ss').format('x');
                let timeDifference = moment(previousTime,'x').fromNow();
                return timeDifference;
            }
        }
    }
</script>
