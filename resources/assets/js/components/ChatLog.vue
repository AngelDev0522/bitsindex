<template>
    <div class="chat-discussion">
        <chat-message v-for="message in messages" :message="message" :key="message.id"></chat-message>
    	<div v-show="messages.length === 0">Start new conversation...</div>
    </div>
    	
</template>

<script>
    export default {
        props:['messages']
    }
</script>
