import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)
export const state = {
  count: 4,
  comments: {},
  commentis: {},
  comment: [],
  children: [],
  user: 0,
  page: 1,
  currentpage: 0,
  offset: 0,
  limit: 0,
  counto: 0,
  counti: 0,
  reply: 0,
  postid: 0,
  userid: {},
  csrf: '',
  status: 'pending'
}

let cookie = document.cookie.split(';').reduce((cookieObject, cookieString) => {
let splitCookie = cookieString.split('=')
try {
cookieObject[splitCookie[0].trim()] = decodeURIComponent(splitCookie[1])
} catch (error) {
cookieObject[splitCookie[0].trim()] = splitCookie[1]
}
return cookieObject
}, []);

const tokeno = cookie['XSRF-TOKEN'];
console.log(tokeno);



export const actions = {
  increment (context) {
    context.commit('increment')
  },

  async addComsSlug (context, datas, dati, dato) {
state.page = state.page++;
let res1 = await fetch('/api/items/' + datas[1] +  '/6/6/'+ state.page++);
console.log(res1);

 let data1 = await res1.json();
console.log(data1);
if(data1[0].pageo !== undefined){ 
state.currentpage = data1[0].pageo;
}
if(data1[0].offsat !== undefined){ 
state.offset = data1[0].offsat;
}

if(res1.status === 201 && data1[0].comments !== undefined){
context.commit('ADD_COMMENTS', data1[0].comments)
}
//state.limit = data1[0].counto;
state.counto = data1[0].counto;
//$offsat = $limit * ($pageo - 1);

 let res = await fetch('/api/items/' + datas[1] +  '/6/6/'+ state.page++);
console.log(res);
 let data = await res.json();
console.log(data);
 if(res.status === 201 && data[0].comments !== undefined){
if(data[0].pageo !== undefined){ 
state.currentpage = data[0].pageo;

}
if(data[0].offsat !== undefined){ 
state.offset = data[0].offsat;
}


 state.user = data[0].user;
 state.counti = data[0].count;
state.limit += data[0].comments.length;

 
 context.commit('ADD_COMMENTS', data[0].comments)
 let ras = await fetch('/api/items/' + datas[1] +  '/6/6/'+ state.page++);
let data3 = await ras.json();
console.log(data3);
 if(ras.status === 201 && data3[0].comments !== undefined){
context.commit('ADD_COMMENTS', data3[0].comments)

}


 }else if(res.status === 201 && data[0] !== undefined){ 
 //console.log(data[0].user);
  
 state.user = data[0].user;       
 state.postid = parseInt(data[0].post.id)

 }
     ///context.commit('ADD_COMMENTS', parents_childs )

    }, 




  async addCommentsSlug (context, datas) {


let res = await axios({
            url: '/api/commentis/' + datas[1],
            method: 'get',
            headers: {
                'X-Requested-With' : 'XMLHttpRequest'
                
            }
        })


 let data = res.data;


 
 if(res.status === 201 && data[0].comments !== undefined ){
 
 
 state.user = data[0].user;
 state.counti = data[0].count;
 
 context.commit('ADD_COMMENTS', data[0].comments)
 }else if(res.status === 201 && data[0] !== undefined){ 
 //console.log(data[0].user);
  
 state.user = data[0].user;       
 state.postid = parseInt(data[0].post.id)

 }
     ///context.commit('ADD_COMMENTS', parents_childs )

    }, 
    
    addComment(context, comment){ 

let ras = axios({
    method: 'post',
    url: '/api/commentis',
    data: comment,
    config: { headers: {'X-Requested-With' : 'XMLHttpRequest' }},
    responseType: 'json'
    })
    .then(function (response) {
      ///context.commit('ADD_COMMENT', response.data) 
        
       if(response.data !== null){
context.commit('ADD_COMMENT', response.data)
context.commit('updateStatus', 'success')
console.log(response.data);
     }else{
context.commit('updateStatus', 'error')

    }


        //context.commit('ADD_COMMENT', response.data)
        //context.commit('updateStatus', 'success')
    })
    .catch(function (response) {
        //handle error
         console.log(response); 
         commit('updateStatus', 'error');
    });
 
    }, 
    editComment(context, comment){

axios({
    method: 'post',
    url: '/api/commentis/edit',
    data: comment,
    config: { headers: {'X-Requested-With' : 'XMLHttpRequest' }},
    responseType: 'json'
    })
    .then(function (response) {
      ///context.commit('ADD_COMMENT', response.data) 
        
       if(response.data !== null){
        context.commit('EDIT_COMMENT', response.data)
        context.commit('updateStatus', 'success')
       }else{
context.commit('updateStatus', 'error')
       }
    })
    .catch(function (response) {
        //handle error
         console.log(response); 
         commit('updateStatus', 'error');
    });


    },  
    setCSRFToken(context, csrf){ 
        context.commit('CSRF_TOKEN', csrf) 
    },
    getFirstComments(context,data){
        context.commit('REFRESH_COMS', data)
    },  
    removeComment(context, comment){
axios({
    method: 'post',
    url: '/api/commentis/delete',
    data: comment,
    config: { headers: {'X-Requested-With' : 'XMLHttpRequest' }},
    responseType: 'json'
    })
    .then(function (response) {
      ///context.commit('ADD_COMMENT', response.data) 
        //console.log(response.data);
        if(response.data !== null){
        context.commit('DELETE_COMMENT', response.data) 
        context.commit('updateStatus', 'success')
        }else{
        context.commit('updateStatus', 'error')   
        }

    })
    .catch(function (response) {
        //handle error
         console.log(response); 
         commit('updateStatus', 'error');
    }); 
     
    },
    replyTo(context, id){
        context.commit('REPLY_TO', id) 
    }  
    
} 

export const getters = {

delite: function (state, getters){
return function (id){
    state.comments[id] = {}
  }
},
useri: state => state.user,
tokeni: state => state.csrf,
users: function (state, getters){
return function (id){
let comment = state.comments[id]
let user = state.userid[comment.id]

if(comment && user){
     return user; 
     
     
    }else{
      return [];
   }


}
        
},
commenti: function (state, getters){
return function (id){
let comment = state.comments[id]

if(comment){
     return comment; 
     
     
    }else{
      return {};
   }    
  }
},
     commentas: state => {
let sortedo = Object.keys(state.comments).sort( (a,b) => b - a).reduce( (ac, item) => { ac.set(parseInt(item),state.comments[parseInt(item)]); return ac } , new Map());

        return sortedo;
        
      },
status: state => state.status,
children: function (state, getters){
return function (id){
let comment = state.comments[id]

if(comment && comment.children){
     return comment.children; 
     
     
    }else{
      return [];
   }




}

},
commentiparent: function (state, getters){
return function (id){
let comment = state.comments[id]
let parent = state.comments[comment.parent_id]
if(parent){
     return parent; 
     
     
    }else{
      return {};
   }    
  }
},
parents: function (state, getters){
var tost = Object.values(state.comments).map((elem, i) => {
    return elem.parent_id;
})

if(tost){
     return tost; 
     
     
    }else{
      return [];
   }

},
inweek: state => {
 var result = Object.assign(
        {},
        ...Object
            .keys(state.comments)
            .filter(k => new Date(state.comments[k].comment_time).getWeek() === new Date().getWeek())
            .map(k => ({ [k]: state.comments[k] }))
    );
let sorteda = Object.keys(result).sort( (a,b) => b - a).reduce( (ac, item) => { ac.set(parseInt(item),result[parseInt(item)]); return ac } , new Map());


return sorteda;

},
notinweek: state => {
 var result = Object.assign(
        {},
        ...Object
            .keys(state.comments)
            .filter(k => new Date(state.comments[k].comment_time).getWeek() < new Date().getWeek())
            .map(k => ({ [k]: state.comments[k] }))
    );
let sortedu = Object.keys(result).sort( (a,b) => b - a).reduce( (ac, item) => { ac.set(parseInt(item),result[parseInt(item)]); return ac } , new Map());


return sortedu;



},
childs: function (state, getters){
return function (id){
let subcomment = state.comments[id]
let subcommenti = subcomment.parent_id
let subcommenta = state.comments[subcomment.parent_id]
console.log(subcommenta);
if(subcommenta && subcommenta.children){

     return subcommenta.children; 
     
     
    }else{
      return [];
   }



}
},
evenOrOdd : state => state.count,

doneTodosCount: (state, getters) => {
return Object.keys(state.comments).length;  
    
  },
postid: (state, getters) => {
    return state.postid  
  }


}

function findById(data, id) {
    function iter(a) {
        if (a.id === id) {
            result = a;
            return true;
        }
        return Array.isArray(a.children) && a.children.some(iter);
    }

    var result;
    data.some(iter);
    return result
}

function findObjectByKey(array, key, value) {
    for (var i = 0; i < array.length; i++) {
        if (array[i][key] === value) {
            return array[i];
        }
    }
    return null;
}

function getMapKeyValueByIndex(obj, idx) {
   var key = Object.keys(obj)[idx];
   return { key: key, value: obj[key] };
} 

function sortObjKeysi(obj) {
  return Object.keys(obj).sort((a,b) => a - b).reduce((result, key) => {
    result[key] = obj[key];
	return result;
  }, {});
}

export const mutations = {
    increment (state) {
      state.count++
    },
incrementi (state) {
      state.page++
    },
 updateStatus(state, status) {
      Vue.set(state, 'status', status);
    },
    CSRF_TOKEN (state, csrf){
      state.csrf = csrf;
console.log(state.csrf)
    },
    REFRESH_COMS(state, data){




    }, 
    ADD_COMMENTS (state, comments){


let obj = {};

let noucoms = comments.sort( function ( a, b ) { return b.id - a.id; } );
noucoms.forEach(comment=> {

obj[comment.id] =  comment

});
var userids = [];
//console.log(state.csrf);
let sorted = Object.keys(obj).sort( (a,b) => b - a).reduce( (ac, item) => { ac.set(item,obj[item]); return ac } , new Map());
sorted.forEach(function(v,k) {
let comment = state.comments[sortObjKeysi(v).id] || {}
comment = {...comment,...v}
state.postid = v.post_id
userids.push([v.id, v.user_id] || [v.id, null])
comment._token = state.csrf
comment.count = state.counti
comment.loaded = true


state.comments = {...sortObjKeysi(state.comments),...{[sortObjKeysi(v).id]: comment}}
state.comments = sortObjKeysi(state.comments)

let parent = comment.parent_id 
});




let obj3 = {};    
userids.forEach(elem => {


obj3[elem[0]] = { id : elem[0], userId: elem[1] }


});

state.userid = obj3;


var io = comments.find(function(o) {// trouve kes parents

if(o.children !== undefined){
  return o.children.find(function(e) {


    return e.id == 928;

  })
}
});
const user = {
    firstName: 'Bramus',
    lastName: 'Van Damme',
    twitter: 'bramus',
    city: 'Vinkt',
    email: 'bramus@bram.us',
};
//const userWithoutEmail = Object.assign({}, user);
//delete userWithoutEmail.email;
const {email, ...userWithoutEmail} = user;
//console.log(userWithoutEmail);

let datos = [{"id":749,"pseudo":"bbbbbbbbbbbbbbbbbbb","post_id":9,"content":"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv","created_at":"2019-06-30 20:03:57","user_id":null,"email":"oppppp@pomm.fr","parent_id":0,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 20:03:57"},{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-06-30 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:26"},{"id":738,"pseudo":"mmmmmmmmmmmmmmmmmm","post_id":9,"content":"aaaaaaaaaaaaaaaaaaaaaaaaaaa","created_at":"2019-06-30 15:47:09","user_id":null,"email":"iguane25@poli.net","parent_id":737,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:09","children":[{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-06-30 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:26"}]},{"id":737,"pseudo":"bbbbbbbbbbbbbbbbbb","post_id":9,"content":"ccccccccccccccccccccccccccccccccccccccc","created_at":"2019-06-30 15:46:35","user_id":null,"email":"iguane25@laposte.net","parent_id":0,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:46:35","children":[{"id":738,"pseudo":"mmmmmmmmmmmmmmmmmm","post_id":9,"content":"aaaaaaaaaaaaaaaaaaaaaaaaaaa","created_at":"2019-06-30 15:47:09","user_id":null,"email":"iguane25@poli.net","parent_id":737,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:09","children":[{"id":739,"pseudo":"kkkkkkkkkkkk","post_id":9,"content":"mmmmmmm","created_at":"2019-06-30 15:47:26","user_id":null,"email":"iguane25@poli.net","parent_id":738,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 15:47:26"}]}]}];
let obja = {};
datos.forEach(comi=> {

obja[comi.id] =  comi

});
let stat = {};
const updateState = (stat, item) => Object.assign({}, stat, { [item.id]: item });

Object.values(obja).map((elem, i) => {

let como = stat[elem.id] || {}
como = {...como,...elem}
stat = {...stat,...{[elem.id]: como}}

})
stat = {...stat,...{770: {"id":770,"pseudo":"bbbbbbbbbbbbbbbbbbb","post_id":9,"content":"vvvvvvvvvvvvvvvvvvvvvvvvvvvvvvvv","created_at":"2019-06-30 20:03:57","user_id":null,"email":"oppppp@pomm.fr","parent_id":0,"pid":9,"pslug":"qsdfgggggggggg","comment_time":"2019-06-30 20:03:57"}}}
///delete stat[770];
const person = {
    firstName: 'Sebastian',
    lastName: 'Markbåge',
    country: 'USA',
    state: 'CA',
};
const { firstName, lastName, ...rest } = person;
//console.log(firstName); // Sebastian
//console.log(lastName); // Markbåge
//console.log(rest);

       
    },
    EDIT_COMMENT(state, comment){
        console.log(comment);

let c = (state.comments[comment.data]);

c._token = state.csrf
if(c.parent_id !== 0){
//console.log(c.parent_id);
let d = (state.comments[comment.data]);
d._token = state.csrf
let b = (state.comments[c.parent_id]);
b._token = state.csrf
if(b !== undefined){
if(b.children === undefined){
          b.children = [];
}
//console.log(b.children);
let indexi = b.children.findIndex((e) => e.id === c.id);
let index = b.children.find((a) => a.id === c.id);
//p.children.splice(indexi,1);
d.content = comment.data1
//console.log(indexi)
//console.log(index)
b.children.splice(indexi,1);
b.children.push(d);
delete (state.comments[c.id])
state.comments = {...state.comments, ...{[d.id]: d}}
//delete (state.comments[c.id])
//state.comments = {...state.comments, ...{[d.id]: d}}


}

}


//console.log(c);

    }, 
    ADD_COMMENT(state, comment){
comment.count = state.counti
console.log(comment);
//state.comments.push(comment);
comment._token = state.csrf

    if(comment.parent_id !== 0){




///state.comments = {...state.comments, ...{[comment.id]: comment}}
    let c = (state.comments[comment.parent_id]);

 ///let u = (state.comments[comment.parent_id].children);
    if(c !== undefined){
          if(c.children === undefined){
          c.children = [];
          } 
    c.children.push(comment);
state.comments[c.id].count++
state.comments = {...state.comments, ...{[comment.id]: comment}}
console.log(state.comments[comment.id].count)
console.log(state.counti)

    }else{
console.log('uu');
//state.comments = {...state.comments, ...{[comment.id]: comment}}  

//console.log(comment.parent_id);
//console.log(comment.id);

 
    }
//console.log(big);
    
    
//console.log(state.comments[0].children);
    }else{
     
    //state.comments = {...state.comments, ...{[comment.id]: comment}}
    
state.comments = {...sortObjKeysi(state.comments),...{[sortObjKeysi(comment).id]: comment}}
state.comments = sortObjKeysi(state.comments)


    }
    
    },

    REPLY_TO(state, reply){

       state.reply = reply;
//console.log(state.reply);
    },

    DELETE_COMMENT(state, comment){
    let id = comment[0].id;
    let parent = comment[0].parent;
    
    //console.log(id);
    //console.log(parent); 
    let p = (state.comments[parent.id]);
    let c = (state.comments[id]); 
    c._token = state.csrf
    //console.log(p);
    if(p !== undefined && p.children !== undefined){ 
    ///console.log(p.children);
    p._token = state.csrf   
    let indexi = p.children.findIndex((d) => d.id === c.id);
    let index = p.children.find((a) => a.id === c.id);
    p.children.splice(indexi,1);
    }
    delete (state.comments[c.id])
    state.comments = {...state.comments, ...state.comments}
    //console.log(state.comments); 
    //console.log(c);
 
    }

}




export default new Vuex.Store({
state,
mutations,
actions,
getters
      
})
