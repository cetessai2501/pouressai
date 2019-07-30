<template>


<div class="observer">
 
</div>


   






</template>
<script>
import Comment from './Comment.vue'
import CommentForm from './CommentForm.vue'
import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'


export default {
  name: 'observer',
   data () {
       return {
           sea: true,
           loading: true,
           observer: null,
           activeColor: 'red',
           percentage: 0,
           lading: true,
           comment: Object
      }
  },
components: { Comment, CommentForm },
props: {
      model: String,
      csrf: String,
      user: {
        type: Number,
        default: 0
      },  
      slug: String,
      id: Number,
      repli: {
        type: Number,
        default: 0
      }  
  },
  computed: {
    isFolder: function () {
      return this.item.children &&
        this.item.children.length
    },
    newuser: function () {
     
      return this.$store.getters.useri
    },
    newtoken: function () {
     
      return this.$store.getters.tokeni
    }, 
    count () {
      return this.$store.state.count
    },
    testi () {
      return this.$store.getters.postid
    },
    testa () {
      
      return this.$store.getters.inweek
    },
    testo () {

    return this.$store.getters.comments;
      
    },
    doneTodosCount () {
    return this.$store.getters.doneTodosCount

     },
     parents: function (){
        return this.$store.getters.parents
    },
    ...mapGetters(['comments'])
  },
methods: {
throttle(callback, delay) {
    var last;
    var timer;
    return function () {
        var context = this;
        var now = +new Date();
        var args = arguments;
        if (last && now < last + delay) {
            // le délai n'est pas écoulé on reset le timer
            clearTimeout(timer);
            timer = setTimeout(function () {
                last = now;
                callback.apply(context, args);
            }, delay);
        } else {
            last = now;
            callback.apply(context, args);
        }
    };
},
onScroll (evt) {
let scroll = evt.target;
let st = scroll.scrollTop;
let sh = scroll.scrollHeight;
let ch = scroll.clientHeight;
let percent = Math.floor((st / (sh - ch)) * 100);

this.percentage = percent;
}
},
destroyed: function () {
this.observer.disconnect();

},
mounted() {

this.observer = new IntersectionObserver(([entry]) => {
      if(entry && entry.isIntersecting){
        
         this.$emit('intersect');
         this.loading = false;
      }
}); 

this.observer.observe(this.$el);



 }

}
</script>
<style scoped>
.observer{
height:1px;
}
.masection{
position:relative;
top:-2%;
height:100px;
margin-left:10%;
margin-right:10%;
line-height:100px;
text-align: center;
}
.myloader{
width: 50px;
height:50px;
display:inline-block;
vertical-align: middle;
position:relative;

}
.loader-quart{
border-radius:50px;
border: 6px solid rgba(0, 0, 0, 0.4);

}
.loader-quart:after{
content:'';
position:absolute;
top:-6px;
left:-6px;
bottom:-6px;
right:-6px;
width: 50px;
height:50px;
border-radius:50px;
border: 6px solid transparent;
border-top-color: #FFF;
-webkit-animation:spin 1s linear 0s infinite;
animation:spin 1s linear 0s infinite;
}
@-webkit-keyframes spin{

      0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
      }

}

@keyframes spin{

      0% {
        -webkit-transform: rotate(0deg);
        transform: rotate(0deg);
      }

      100% {
        -webkit-transform: rotate(360deg);
        transform: rotate(360deg);
      }

}
</style>



