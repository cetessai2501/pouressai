<template>

<div :id="comment.id" class="comment">



<div class="avatar"><img v-bind:src="'http://www.gravatar.com/avatar/' + MD5(comment.email)" alt="" v-bind:title="' ' + test(comment.email)"/></div>
<div class="content comment">
<div class="author">{{ comment.pseudo }}</div>
<div class="metadata">
<span class="date"> {{ TimeAgo(new Date(comment.comment_time)) }} </span>

</div>
<div class="text">
       {{ comment.content }}
</div>

</div>
<div class="actions">
<a href="#" @click.prevent="displayForm" v-bind:style="{ color: activeColor }">{{ comment.parent_id == 0 ? 'Commenter' : 'Répondre'}}</a> 

<comment-form :id="comment.id" :repli="0" :delate="0" :postid="comment.post_id" :usrid="newuser" :tokena="newtoken" :seen="true" v-if="comment.parent_id == 0 && delate== 0"></comment-form>
<comment-form :id="comment.id" :repli="0" :delate="1" :postid="comment.post_id" :usrid="newuser" :tokena="newtoken" :seen="true" v-else="comment.parent_id == 0 && delate== 1"></comment-form>

<a href="#" :delate="newdelate" @click.prevent="testForm" v-if="users.userId == newuser && newuser !== 0" v-bind:style="{ color: activeCol }"> Delete  </a>

<a href="#" :delate="2" @click.prevent="testFormi" v-if="users.userId == newuser && newuser !== 0"> Edit  </a>

<comment-formi :id="comment.id" :repli="0" :delate="2" :postid="comment.post_id" :usrid="newuser" :tokena="newtoken" :seen="true" v-if="comment.parent_id == 0 && delate !== 1 && users.userId == newuser"></comment-formi>
 
 </div>
  <div class="comments">

<message :message="message" v-for="message in messages"  :key="message.id"></message>


  </div>


</div> 

</template>
<style scoped>
.comment.reveala{
opacity: 0;
transform: translateY(30px);

}
.comment.reveal-loaded{
   transition: 1s cubic-bezier(.5, 0, 0, 1);
}

.info {
  
  
}
.content.comment{
border: 2px solid black;
border-radius: 5px;
}


</style>

<script type="text/babel">
import axios from 'axios'
import Message from './Message.vue'
import CommentForm from './CommentForm.vue'
import CommentFormi from './CommentFormi.vue'

import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'

export default {
  name: 'comment',
   data () {
       return {
          isNinja: true,
          delate: 0,
          infoclass : 'info',
          activeColor: 'DodgerBlue',
          activeCol: 'red'
         
      }
  }, 
  components: { CommentForm, Message, CommentFormi },
  props: {
      id: Number,
      comment: Object,
      repli: {
          type: Number,
          default: 0
      },
      postid: {
          type: Number
      },
      usrid: {
          type: Number,
          default: 0
      }   
  
  },
  computed: {
    newdelate: function () {
     
      return this.delate + 1
    },
    newuser: function () {
     
      return this.$store.getters.useri
    },
    newtoken: function () {
     
      return this.$store.getters.tokeni
    }, 
    newdelatee: function () {
     
      return this.delate + 2
    },
    messages: function (){
        return this.$store.getters.children(this.comment.id)
    },
    childs: function (){
        return this.$store.getters.childs(this.comment.id)
    },
    users: function (){
        
        return this.$store.getters.users(this.comment.id)
    }, 
    ...mapGetters(['commentas']),
    ...mapState(['status'])  
  },
  watch: {
    'this.comment.id': function (){
         this.loadComments(comment)
    },
    'this.comment.id': function (){
         this.ladiComments(comment)
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
     loadComments: function (comment){
           this.$store.dispatch('addComment', comment  ).then(() => {
              console.log(comment);
          })  
     },
     ladiComments: function (comment){
           this.$store.dispatch('removeComment', comment  ).then(() => {
              console.log(comment);
          })  
     }, 
     ladoComments: function (comment){
           
     }, 
     updateDelate: function (delate) {
    	Vue.set(delate, 1)
      console.log(delate);
    },
    updateCoachStatus: function(event){
         console.log(this.delate);
         this.isDelate = 1; 
         return this.isDelate;
         
            
     },
     replyTo(x){

CommentForm.props.seen == false;

this.$store.dispatch('replyTo', x)
     },
     displayForm: function (event){

event.target.parentNode.childNodes[2].style.display = "block";
//console.log(event.target.parentNode);


     },
     testForm: function (event){
//event.target.parentNode.childNodes[2].style.display = "none";
this.delate = 1;
//console.log(this.comment);
const formData = new FormData();
formData.append('_csrf', this.comment._token);
formData.append('parentid', this.comment.id);

this.$store.dispatch('removeComment', formData  ).then(() => {

//console.log(this.$store.state.comments);


})

     },
testFormi: function (event){
console.log(event.target);
this.delate = 2;

event.target.parentNode.childNodes[8].style.display ="block";
var myoptions = [this.useri, this.$parent._props.slug];



},
created: function (){
 
},

     mounted: function (){


//console.log(this.$store.state.comments);         
  //var myKeys = Object.keys(this.$store.state.comments)     
//var matchingKeys = myKeys.filter(function(key){ return key.indexOf(1022) !== -1 });



          
           
    }

  }
  
}



</script>
