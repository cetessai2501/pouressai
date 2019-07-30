<template>
<div v-show="see">

<h3 class="ui header dividing" >Modifier le contenu</h3>
<form action="" class="ui form" @submit.prevent="sendCommenti">
<div class="two fields">
<div class="field">
<label >votre pseudo</label>
<input type="text" v-model="comments.pseudo">
</div>
<div class="field">
<input id="element_id" type="hidden"   v-model="comments.id">
<input type="hidden" v-model="comments._token">
</div>
<div class="field">
<label >votre email</label>
<input id="email" type="text"  v-model="comments.email">
</div>
</div>

<div class="field">
<label >votre commentaire</label>
<textarea  v-model="comments.content" cols="30" rows="4"></textarea>

</div>

<button class="ui blue labeled submit icon button"><i class="icon edit"></i>Soumettre</button>
<button class="ui grey labeled submit icon button" type="button">
<i class="icon cancel"></i>Annuler
</button>
</form>
</div>

</template>

<script type="text/babel">
import { addComment  } from '../store/store.js'
import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'
import Comment from './Comment.vue'
import Message from './Message.vue'

export default {
  name: 'comment-formi',
  data () {
       return {
          see: false,
          pseudo: '',
          email: '',
          content: '',
          token: '' 
      }
  }, 
  components: { Comment, Message },  
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
          default: ''
      }
      
      
  },
  computed: {
comments: function (){
        
        return this.$store.getters.commenti(this.id) 
    },
messagis: function (){
         console.log(this.id);
         return this.$store.getters.children(this.id)
        
    },
...mapGetters(['commenti']),
...mapGetters(['children']),
  },
  methods: {
    sendCommenti: function (event){

const formData = new FormData();
formData.append('element_id', event.target.childNodes[0].childNodes[2].childNodes[0]._value);

formData.append('contenti', event.target[4]._value);
formData.append('_csrf', this.comments._token);
this.$store.dispatch('editComment', formData  ).then(() => {
event.target.parentNode.style.display = "none"
 

})


},
hideForm: function (event){
console.log(event.target.parentNode);
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
