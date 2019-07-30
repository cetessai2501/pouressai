<template>

<div v-show="see">

<h3 class="ui header dividing" v-if="repli == 0 && delate == 0" >Votre commentaire</h3>
<h3 class="ui header dividing" v-else-if="repli !== 0 && delate == 0" >Votre reponse</h3>
<h3 class="ui header dividing" v-else-if="repli !== 0 && delate == 1" >Votre ra</h3>
<h3 class="ui header dividing" v-else>Votre </h3>
<form action=""  class="ui form" @submit.prevent="sendComment">
 <div class="two fields">
   <div class="field">
   <label >votre pseudo</label>
   <input type="text" v-model="pseudo">
    </div>
   <input type="hidden" v-model="token">
   <div class="field">
   <label >votre email</label>
   <input type="text" v-model="email">
   </div>
</div>
<div class="field">
<label >votre commentaire</label>
<textarea  v-model="content" cols="30" rows="4"></textarea>
</div>

<button class="ui blue labeled submit icon button"><i class="icon edit"></i>
{{ repli == 0 ? 'Commenter' : 'Répondre' }}

</button>
<button class="ui grey labeled submit icon button" type="button">
<i class="icon cancel"></i>Annuler
</button>
</form>
</div>

</template>
<script type="text/babel">
import { addComment } from '../store/store.js'
import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'
import Comment from './Comment.vue'

export default {
  name: 'comment-form',
  data () {
       return {
          see: false,
          pseudo: '',
          email: '',
          content: '',
          token:''
      }
  }, 
  props: {
       id: Number,
       delate: {
          type: Number,
          default: 0
      },
      repli: {
          type: Number,
          default: 0
      },
      seen: false,
      postid: {
          type: Number
      },
      usrid: {
          type: Number,
          default: 0
      },
      tokena: {
          type: String,
          default: this.token
      }
      
  },
  computed: {
newtoken: function () {
      this.token = this.$store.getters.tokeni;
      return this.token
    }, 
...mapGetters(['delite'])

  },
  methods: {
    sendComment: function (event){
console.log(this.repli);
console.log(this.id);
if(this.repli === 0 && this.id === 0){

const formData = new FormData();
formData.append('pseudo', this.pseudo);
formData.append('content', this.content);
formData.append('email', this.email);
formData.append('parent_id', this.id);
formData.append('post_id', this.postid);
formData.append('user_id', this.usrid);
formData.append('_csrf', this.tokena);
//console.log(this.tokena);

this.$store.dispatch('addComment', formData  ).then(() => {
var self = this;
this.pseudo = '';
this.email = '';
this.content = '';
this.token = '';
//console.log(event.target.parentNode.parentNode);



})

}else if(this.repli === 0 && this.id !== 0 ) {
const formData = new FormData();
formData.append('pseudo', this.pseudo);
formData.append('content', this.content);
formData.append('email', this.email);
formData.append('parent_id', this.id);
formData.append('post_id', this.postid);
formData.append('user_id', this.usrid);
formData.append('_csrf', this.tokena);
this.$store.dispatch('addComment', formData ).then(() => {


//console.log(event.target.parentNode.parentNode.childNodes);
event.target.parentNode.style.display = "none";
})
}else{
const formData = new FormData();
formData.append('pseudo', this.pseudo);
formData.append('content', this.content);
formData.append('email', this.email);
formData.append('parent_id', this.repli);
formData.append('post_id', this.postid);
formData.append('user_id', this.usrid);
formData.append('_csrf', this.tokena);
this.$store.dispatch('addComment', formData  ).then(() => {



event.target.parentNode.style.display = "none";
})



}



},
hideForm: function (event){
//console.log(event.target.parentNode.parentNode);
event.target.parentNode.style.display = "none";



     },
  change_delate(new_name) {
   this.$emit('delate-updated', new_name)
    }



  },
  mounted: function (){

         
         
       

          
                   


          
           
    }




  
}


</script>
