<template>
     <div >
         <div class="col-md-8">
                <h2> derniers commentaires </h2>   
                <div class="list-group-item cetitem" style="margin-bottom:10px;" v-for="comment in comments">
<img v-bind:src="'http://www.gravatar.com/avatar/' + MD5(comment.email)" alt="Avatar" /><span class="date" style="color:red;font-weight:bold;"> {{ TimeAgo(new Date(comment.comment_time)) }} </span><br>  
                    <span><strong>  {{ comment.pseudo }} </strong></span><br>  
                    <span class="monspan">  {{ comment.pname }} </span><br> 
                    <a style="text-decoration:none;" v-bind:href="url(comment)"> Voir les commentaires </a>
                </div>
          
        </div>
    </div>
</template>

<script>


    export default {
     data () {
     return {
        comments: [], 
        url: function (comment){
            
                 let url = 'blog/' + comment.pslug;
                 return url; 
            
         }  
       }
     }, 
     props: {
      
       
  
     },  
     methods: {
       MD5: function (x) {
         return window.gravi(x, 30);
       },
       TimeAgo: function (x) {
        return window.timeAgo(x);
       }
     },  
     async mounted() {
            let ras = await fetch('/api/comments');
            let data3 = await ras.json();
            
            this.comments = data3;
            
        }
      
}
</script>

