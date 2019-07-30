<template>

<div class="comment">

 <div class="avatar"><img v-bind:src="'http://www.gravatar.com/avatar/' + MD5(message.email)" alt="" v-bind:title="' ' + test(message.email)"/></div>
 <div class="content message">
  <div class="author">{{ message.pseudo }}</div>
  <div class="metadata">
   <span class="date"> {{ TimeAgo(new Date(message.comment_time)) }}</span>
  </div>
<div class="text">
{{ message.content }}
</div>
</div>
<div class="actions">

<a href="#" @click.prevent="displayForm" v-bind:style="{ color: activeColor }"> {{ message.parent_id == 0 ? 'Commenter' : 'Répondre' }}</a> 

<comment-form :id="message.id" :repli="message.id" :delate="0" :postid="message.post_id" :usrid="newuser" :seen="true" v-if="message.parent_id !== 0 && delate== 0"></comment-form>
<comment-form :id="message.id" :repli="message.id" :delate="1" :postid="message.post_id" :usrid="newuser" :seen="true" v-else="message.parent_id !== 0 && delate== 1"></comment-form>

<a href="#" :delate="newdelate" @click.prevent="testForm" v-if="users.userId == newuser && newuser !== 0" v-bind:style="{ color: activeCol }"> Delete  </a> 

<a href="#" :delate="3" @click.prevent="testFormi" v-if="users.userId == newuser && newuser !== 0"> Edit  </a>

<comment-formi :id="message.id" :repli="message.id" :delate="3" :postid="message.post_id" :usrid="newuser" :tokena="newtoken" :seen="true" v-if="message.parent_id !== 0 && delate !== 2 && users.userId == newuser"></comment-formi>

 </div>
  <div class="comments">

<message :message="message" v-for="message in messages"  :key="message.id"></message>

  </div>

</div> 

</template>
<style scoped>

.content.message {
border: thin dotted black;
}
</style>

<script type="text/babel">
import axios from 'axios'
import CommentForm from './CommentForm.vue'
import CommentFormi from './CommentFormi.vue'

import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'

export default {
  name: 'message',
   data () {
       return {
          isNinja: true,
          delate: 0,
          activeColor: 'DodgerBlue',
          activeCol: 'red'
         
      }
  }, 
  components: { CommentForm, CommentFormi },
  props: {
      id: Number,
      message: Object,
      repli: {
          type: Number,
          default: 0
      },
      postid: {
          type: Number
      },
      usrid: {
          type: Number
          
      }   
  
  },
  computed: {
    newdelate: function () {
     
      return this.delate + 1
    },
    newdelatee: function () {
     
      return this.delate + 2
    },
    newtoken: function () {
     
      return this.$store.getters.tokeni
    },
    newuser: function () {
     
      return this.$store.getters.useri
    }, 
    newdelateee: function () {
     
      return this.delate + 3
    }, 
    formVisible: function () {
      return this.repli == this.message.id
        
    },
    messages: function (){
        
        return this.$store.getters.children(this.message.id)
    },
    users: function (){
                
        return this.$store.getters.users(this.message.id)
    },
    childs: function (){
        return this.$store.getters.childs(this.message.id)
    },
 
    ...mapGetters(['children']),
    ...mapState(['status'])  
  },
  watch: {
    'this.message.id': function (){
        this.ladiComments(this.message)
    }
  }, 
  methods: {
     MD5: function (x) {
     return window.grav(x, 50);
     },
     TimeAgo: function (x) {
     return window.timeAgo(x);
     },
     test: function(x){
       return x;
     },
     ladiComments: function (message){
           const formData = new FormData();
           formData.append('_csrf', this.message._token);
           formData.append('parentid', this.message.id);
           this.$store.dispatch('removeComment', formData  ).then(() => {
              //console.log(this.$store.state.comments);
          })  
     },   
     loadMessages: function (message){
this.$store.dispatch('addComment', this.message  ).then(() => {
//console.log(message);
})

     }, 
     replyTo(x){

CommentForm.props.seen == false;

this.$store.dispatch('replyTo', x)
     },
     displayForm: function (event){
event.target.parentNode.childNodes[2].style.display = "block";



     },
     testForm: function (event){

//console.log(this.message);

this.ladiComments(this.message);

},
testFormi: function (event){
//console.log(event.target);
this.delate = 3;
//console.log(this.delate);

event.target.parentNode.childNodes[8].style.display ="block";







},
     mounted: function (){


console.log('mountedi')


         
         
       

          
                   


          
           
    } 

  }
  
}



</script>
