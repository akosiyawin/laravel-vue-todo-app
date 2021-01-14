<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h3 class="text-center">My List of Todos</h3>
                <form @submit.prevent="saveTodo()" class="mt-3 form-groups">
                    <div class="mb-3 input-group">
                        <input v-model="form.title"
                               :class="{'is-invalid' : form.errors.has('title')}"
                               type="text" class="form-control"
                               placeholder="Insert Todos"
                               aria-label="Recipient's username"
                               @keydown="form.errors.clear('title')"
                               aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" type="submit">Add Todo</button>
                        </div>
                        <div class="invalid-feedback" v-text="form.errors.get('title')"></div>
                    </div>
                </form>
                <div class="todos-collections table-responsive" style="height: 40vh">
                    <div class="bg-white mb-1 p-2 d-flex align-items-center" v-for="todo in todos" :key="todo.id">
                        <span v-if="todo.is_completed" @click="updateCompleted(todo.id,!todo.is_completed)"
                              class="btn btn-success mr-2">Done</span>
                        <span v-if="!todo.is_completed" @click="updateCompleted(todo.id,!todo.is_completed)"
                              class="btn btn-warning mr-2">Not</span>
                        <p v-if="onEditMode !== todo.id" class="m-0 p-0 flex-grow-1">{{ todo.title }}</p>
                        <div v-if="onEditMode === todo.id" class="flex-grow-1">
                            <input v-model="editForm.title" class="form-control"
                                   :class="{'is-invalid' : editForm.errors.has('title')}" type="text">
                            <div class="invalid-feedback" v-text="editForm.errors.get('title')"></div>
                        </div>
                        <span class="btn btn-outline-info mr-2" v-if="onEditMode !== todo.id" @click="toggleEdit(todo)">Edit</span>
                        <span class="btn btn-outline-info mr-2" v-if="onEditMode === todo.id"
                              @click="updateTitle(todo)">Done</span>
                        <span class="btn btn-outline-danger" v-on:click="onDelete(todo)">Remove</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Vue from 'vue'
import VueSweetalert2 from 'vue-sweetalert2';
Vue.use(VueSweetalert2);

export default {
    name: 'Todo',
    data() {
        return {
            form: new Form({
                title: ""
            }),
            todos: [],
            onEditMode: null,
            editForm: new Form({
                title: ''
            })
        }
    },

    methods: {
        saveTodo() {
            const formData = new FormData()
            formData.append('title', this.form.title)
            axios.post('/api/todos', formData)
            .then(r => {
                this.loadTodos()
                this.form.reset()
            })
            .catch(err => {
                this.form.errors.record(err.response.data.errors)
            })
        },
        loadTodos() {
            axios.get('/api/todos')
            .then(r => {
                this.todos = r.data
            })
        },
        updateCompleted(id, status) {
            axios.patch('/api/todos/' + id, {status})
            .then(() => this.loadTodos())
        },
        updateTitle(todo) {
            axios.patch('/api/todos/' + todo.id, {title: this.editForm.title})
            .then(() => {
                this.loadTodos()
                this.onEditMode = null
            }).catch(r => {
                this.editForm.errors.record(r.response.data.errors)
            })
        },
        toggleEdit(todo) {
            this.editForm.errors.clear('title')
            this.onEditMode = todo.id
            this.editForm.title = todo.title
            this.loadTodos()
        },
        onDelete(todo){
            this.$swal({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = new Form()
                    form.delete('/api/todos/'+todo.id)
                    .then(()=>{
                        this.loadTodos()
                        this.$swal(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    })
                }
            })
        }
    },

    mounted() {
        this.loadTodos()
    }
}
</script>
