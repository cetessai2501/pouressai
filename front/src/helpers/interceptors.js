import axios from 'axios';
import store from '../store/store'

export default function setup(store) {

let cookie = document.cookie.split(';').reduce((cookieObject, cookieString) => {
   		let splitCookie = cookieString.split('=')
   		try {
   		  cookieObject[splitCookie[0].trim()] = decodeURIComponent(splitCookie[1])
   		} catch (error) {
   			cookieObject[splitCookie[0].trim()] = splitCookie[1]
   		}
   		return cookieObject
   	}, [])

let essai = cookie['XSRF-TOKEN']
store.dispatch('setCSRFToken', essai).then(() => {

})   
//let state.csrf = cookie['XSRF-TOKEN']










}

