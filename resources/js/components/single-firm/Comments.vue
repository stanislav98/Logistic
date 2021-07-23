<template>
	<div class="comment_wrapper">
		<div class="heading_single_component">
			<h4 class="sub_title">Коментари</h4>
			<button @click.prevent="commentState == 1 || commentState == 2 ? commentState = 0 : commentState = 2" class="btn_second">{{text[commentState] }}</button>
		</div>
		<!-- Add Comment -->
		<div class="card_box add_comment" v-if="commentState === 2">
			<div class="wrapper">
				<textarea type="text" name="body" v-model="commentBody" placeholder="Въведете вашият коментар" />
				<button class="btn" @click.prevent="addComment">Добавяне</button>
			</div>
		</div>
		<!-- End Add Comment -->

		<!-- View Comments -->
		<ul class="comments-list card_box">
			<li class="single-comment div_underlined" v-for="comment in comments" :key="comment.id" v-if="">
				<div class="comment_heading">
					<a :href="`/dashboard/user/${comment.user_id}`" class="user">
						<span class="material-icons">person</span>
						{{ getCommentAuthorName(comment.user_id)  }}
					</a>
					<div v-if="comment.user_id === user.id">
						<span class="material-icons edit" @click="editComment(comment.id,1)">edit</span>
						<span class="material-icons delete" @click="deleteComment(comment.id)">delete</span>
					</div>
				</div>
				<p class="date" v-if="comment.created_at">
					<span class="material-icons">today</span>
					<span class="date_added">Дата на публикуване : {{ formatDate(comment.created_at) }} </span>
				</p>
				<p class="comment_body" v-if="comment.inEdit === 0">
					<span>{{ comment.body }}</span>
				</p>
				<div class="edit" v-if="comment.inEdit === 1">
					<textarea name="edit_comment" id="edit_comment" v-model="comment.body" />
					<div class="btns_wrapper">
						<button @click="editComment(comment.id,0)" class="btn_second">Изход от редакция</button>
						<button @click="edit(comment.id,comment.body)" class="btn_second">Редактирай</button>
					</div>
				</div>
			</li>
		</ul>
		<!-- /View Comment -->

	</div>
</template>
<script>
	export default {
		computed: {
			onlineUsers() {
				return this.$store.state.onlineUsers
			},
			user() {
				return this.$store.state.user
			},
		},
		data() {
			return {
				comments: [],
				commentBody: '',
				commentState: 0, // 0 -> view, 1-> edit , 2 -> add new comment
				text: ['Добавяне на коментар','Изход редакция','Скриване']
			}
		},
		methods: {
			editComment(commentId,flagEdit) {
				let index = this.comments.find(o => o.id === commentId)
				index.inEdit = flagEdit
			},
			edit(commentId,body) {
				axios.put(`/api/comments/${commentId}`,{body: body})
				.then(resp => {
					this.editComment(commentId,0)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			deleteComment(commentId) {
				axios.delete(`/api/comments/${commentId}`)
				.then(resp => {
					//filter e.g remove the comment
					this.comments = this.comments.filter(e => e.id !== commentId)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			getCommentAuthorName(authorId) {
				if(authorId === this.user.id) {
					return this.user.name
				}
				const author = this.onlineUsers.find((e) => e.id === authorId)
				return author.name
			},
			fetchComments() {
				axios.get(`/api/comments/${this.$route.params.id}`)
				.then(resp => {
					let comments = resp.data.comments
					comments = comments.map(obj=> ({ ...obj, inEdit: 0 }))
					this.comments = comments
				})
				.catch(err => {
					this.$store.commit('set_notification',err.response.data.notification)
				})
			},
			formatDate(strDate) {
				return new Date(strDate).toLocaleString();
			},
			addComment() {
				axios.post('/api/comments/', {'body': this.commentBody, 'user_id': this.user.id, 'firm_id': this.$route.params.id})
				.then(resp => {
					const comment = resp.data.comment
					comment.inEdit = 0
					this.commentBody = ''
					this.comments.unshift(comment)
					this.$store.commit('set_notification',resp.data.notification)
				})
				.catch(err => {
					if(err.response.data.notification) {
						this.$store.commit('set_notification',err.response.data.notification)
					} else {
						const errors = err.response.data.errors
						const msg = errors[Object.keys(errors)[0]][0]
						const errorObj = {
							'msg': msg ,
		        			'type': 0, 
		        			'active': 1
						}
						this.$store.commit('set_notification', errorObj)
					}
				})
			}
		},
		created() {
			this.fetchComments()
		}
	}
</script>