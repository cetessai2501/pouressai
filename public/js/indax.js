let Vue = require('vue');

const Discord = require('discord.js')




Vue.component('example', require('./components/Example.vue').default);
var app = new Vue({
  el: '#app'
});
const bot =  new Discord.Client()

bot.on('ready', () => {
    console.log("Connected as " + bot.user.tag)
})


bot.on('message', function (msg){
console.log(msg.content);
if(msg.content === '!ping'){
msg.reply('pong');
}
})
bot.login('token');
