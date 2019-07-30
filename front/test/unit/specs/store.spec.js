import 'babel-polyfill'
import { mutations } from '@/store/store.js'



describe('mutations', () => {
  it('ADD_COMMENTS', () => {
    const state = {comments: [1, 2]} 
    mutations.ADD_COMMENTS(state, [3,4,5])
    expect(state.comments.length).to.equal(5) 
  }) 

  

  it('ADD_COMMENT', () => {
    const state = {comments: [{id: 10}]} 
    mutations.ADD_COMMENT(state, {parent_id: 10 , text: 'kkkkkkkk'})
    expect(state.comments[0].children.length).to.equal(1)
    expect(state.comments.length).to.equal(1)
  })

  it('DELETE_COMMENT', () => {
    const state = {comments: [{id: 1}, {id: 3}, {id: 4}]} 
    mutations.DELETE_COMMENT(state, {id: 3} )
    expect(state.comments.length).to.equal(2)
    expect(state.comments[1].id).to.equal(4)  
  })

})
