<template>
<div class="ui comments">

<comment :comment="comment" v-for="comment of commentas.values()" v-if="comment.parent_id == 0" :key="comment.id"></comment>


<comment-form :id="0" :repli="0" :delate="0" :postid="testi" :usrid="newuser" :tokena="newtoken" v-show="sea"></comment-form>






</div>
</template>
<style scoped>
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
.scrollbar{


}
</style>


<script type="text/babel">
import Comment from './Comment.vue'
import CommentForm from './CommentForm.vue'
import CommentFormi from './CommentFormi.vue'
import Observer from './Observer.vue'
import { mapState, mapMutations, mapActions, mapGetters } from 'vuex'
import Vue from 'vue'
import Message from './Message.vue'

Vue.directive('square', function (el, binding) {
  el.innerHTML = Math.pow(binding.value, 2);
});
class MyStore {
   constructor (){
      this.state = {
          count: 0
      }
   }

}



export default {
  data () {
     return {
       sea: true,
       loading: true,
       observer: null,
       lading: true,
       limit: 6,
       offset: 0,
       imageLoadLimit: 6,
       items: {},
       item: Object,
       comment: Object,
       nextItem: 1,
       state: Object,
       comments: [], 
       children: [],
       commentos: [], 
       perPage: 10,
       page: 1,
       pages: [],
       counto: 0,
       loding: true,
       percentage: 0, 
       saa: false,
       locked: false,
       first: 0,
       test: []
     }
  },
 components: { Comment, CommentForm, CommentFormi },
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
    counta() {
      return this.$store.getters.doneTodosCount
    },
    testi: function() {
       return this.$store.state.postid;
    }, 
    
    ...mapGetters(['commentas'])
  },
  
methods: {
    async intersected(){
const listElm = document.querySelector('#infinite-list');
var myoptions = [this.$options.propsData.user, this.$options.propsData.slug];
  


    // Initially load some items.
   


            
    }, 
close (){
this.$emit('close');
}, 
async paginate (comments) {
      
      /** This is only for this demo, you could 
        * replace the following with code to hit 
        * an endpoint to pull in more data. **/
      this.loading = true;
let numberOfPages = Math.ceil(this.counto / this.perPage);
 for (let index = 1; index <= numberOfPages; index++) {
        this.pages.push(index);
      }

let page = this.page;
      let perPage = this.perPage;
      let from = (page * perPage) - perPage;
      let to = (page * perPage);
return  comments.slice(from, to);



},
async fetchItems(imageLimit, offset) {
            const response = await fetch(
                `https://api.giphy.com/v1/gifs/search?q=cats&api_key=Z5klMMIHxiHVXXa9LSkm5s7Fz0xPJ3n1&limit=${imageLimit}&offset=${offset}`
            );

            const json = await response.json();

            return json;
        },

    scrollBot () {
     let $mess = this.$el.children[1]

     this.$nextTick(() => {
          $mess.scrollTop = $mess.scrollHeight;
      })
    }, 
    scrollBat () {
     let $mess = this.$el.children[1]

     this.$nextTick(() => {
           $mess.scrollTop = $mess.scrollHeight - $mess.scrollHeight;
      })
    }, 
onScrollu () {
this.$el.firstChild.style.display = "none";
this.lading = false
console.log(this.$el.scrollTop)


},
onScrollo () {
if( 0 < this.$el.scrollTop < 200){
this.$el.firstChild.style.display = "none";

this.$el.firstChild.style.display = "block";
this.lading = false

if( this.$el.scrollTop < 200){
this.$el.addEventListener('scroll', this.onScrolli)

}





}
}, 
onScrolli () {
if(this.$el.scrollTop > 0){

this.$el.firstChild.style.display = "none";


}

}, 
scrollDetect(){
  var lastScroll = 0;

      let currentScroll = this.$el

      if (currentScroll > 0 && lastScroll <= currentScroll){
        lastScroll = currentScroll;
        console.log('do')
      }else{
        lastScroll = currentScroll;
        console.log('up')
      }
  
},
findScrollDirectionOtherBrowsers(event){
        var delta;
console.log(event)
        if (event.wheelDelta){
            delta = event.wheelDelta;
        }else{
            delta = -1 * event.deltaY;
        }

        if (delta < 0){
            console.log("DOWN");
this.lading = true
this.$el.firstChild.style.display = "none";







        }else if (delta > 0){
            console.log("UP");
this.lading = false
this.$el.firstChild.style.display = "none";



        }

},
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
faireQuelque(position_scroll, h) {
var d = document;
console.log('up')
console.log(position_scroll)
console.log(this.$el.scrollTop)



},
faireQuelqueChose(position_scroll, h) {
var d = document;
console.log('down')
if(position_scroll < 1000){
console.log(position_scroll)
console.log(this.$el.scrollTop)

}else{
console.log('hhh')



}

},
scrollDistance (callback, refresh) {

	// Make sure a valid callback was provided
	if (!callback || typeof callback !== 'function') return;

	// Variables
	var isScrolling, start, end, distance;

	// Listen for scroll events
	window.addEventListener('scroll', function (event) {

		// Set starting position
		if (!start) {
			start = window.pageYOffset;
		}

		// Clear our timeout throughout the scroll
		window.clearTimeout(isScrolling);

		// Set a timeout to run after scrolling ends
		isScrolling = setTimeout(function() {

			// Calculate distance
			end = window.pageYOffset;
			distance = end - start;

			// Run the callback
			callback(distance, start, end);

			// Reset calculations
			start = null;
			end = null;
			distance = null;

		}, refresh || 66);

	}, false);

},
onScroll (evt) {
console.log(evt);


},
    getCommentis () {
      var comments = this.$store.state.comments
      console.log(comments);
      return comments
    }

  },
makeFolder: function () {
      if (!this.isFolder) {
      	this.$emit('make-folder', this.item)
        this.isOpen = true
      }
  },
  MD5: function (x) {
     return window.grav(el, 20);
  },
visibleY: function(el){
  var rect = el.getBoundingClientRect(), top = rect.top, height = rect.height, 
    el = el.parentNode;


  do {
    rect = el.getBoundingClientRect();
    if (top <= rect.bottom === false) return false;
    // Check if the element is out of view due to a container scrolling
    if ((top + height) <= rect.top) return false
    el = el.parentNode;
  } while (el != document.body);
  // Check its within the document viewport
  return top <= document.documentElement.clientHeight;
},

  updated: function () {
    
  },
  destroyed: function () {
     //this.$el.children[1].removeEventListener('scroll', this.onScroll)
  },
   mounted() {

var myoptions = [this.$options.propsData.user, this.$options.propsData.slug];

         this.$store.dispatch('addCommentsSlug', myoptions).then(() => {
          
            
         });




     
       
             


          
           
    }
 

  
}

</script>
