<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-list"></i> User List</h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-success" @click="newModal">
                      <i class="fa fa-plus"></i>
                      Add New
                  </button>
                </div>
              </div>
             
              <div class="card-body table-responsive p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>SL</th>
                      <th>Type</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th class="text-center">Email Verified</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(user, index) in users.data" :key="user.id">
                      <td>{{ index+1 }}</td>
                      <td class="text-capitalize">{{user.type}}</td>
                      <td class="text-capitalize">{{user.name}}</td>
                      <td>{{user.email}}</td>
                      <td :inner-html.prop="user.email_verified_at | yesno" class="text-center"></td>
                      <td class="text-center">

                        <a href="#" @click="editModal(user)" class="btn btn-success">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <a href="#" @click="deleteUser(user.id)" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Delete
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
             
               <div class="card-footer" style="background-color: #fff;">
                  <pagination :data="users" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
           
          </div>
        </div>


        <div v-if="!$gate.isAdmin()">
            <not-found></not-found>
        </div>

      
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" v-show="!editmode">Create New User</h5>
                    <h5 class="modal-title" v-show="editmode">Update User's Info</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateUser() : createUser()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Name <span style="color: #fb0606">*</span></label>
                            <input v-model="form.name" type="text" name="name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Enter name" required>
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <label>Email <span style="color: #fb0606">*</span></label>
                            <input v-model="form.email" type="text" name="email" class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Enter email" required>
                            <has-error :form="form" field="email"></has-error>
                        </div>
                    
                        <div class="form-group">
                            <label>Password <span style="color: #fb0606">*</span></label>
                            <input v-model="form.password" type="password" name="password" class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Enter password" autocomplete="false" required>
                            <has-error :form="form" field="password"></has-error>
                        </div>
                    
                        <div class="form-group">
                            <label>User Role <span style="color: #fb0606">*</span></label>
                            <select name="type" v-model="form.type" id="type" class="form-control" :class="{ 'is-invalid': form.errors.has('type') }" required>
                                <option value="">Select User Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">Standard User</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                    </div>
                    <div class="modal-footer" style="background-color: aliceblue">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        <button v-show="editmode" type="submit" class="btn btn-success"><i class="fa fa-edit"></i> Update</button>
                        <button v-show="!editmode" type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
  </section>
</template>

<script>
    export default {
        data () {
            return {
                editmode: false,
                users : {},
                form: new Form({
                    id : '',
                    type : '',
                    name: '',
                    email: '',
                    password: '',
                    email_verified_at: '',
                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('api/user?page=' + page).then(({ data }) => (this.users = data.data));

                  this.$Progress.finish();
            },
            updateUser(){
                this.$Progress.start();
                // console.log('Editing data');
                this.form.put('api/user/'+this.form.id)
                .then((response) => {
                    // success
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                        //  Fire.$emit('AfterCreate');

                    this.loadUsers();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(user){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(user);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            deleteUser(id){
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {

                        // Send request to the server
                         if (result.value) {
                                this.form.delete('api/user/'+id).then(()=>{
                                        Swal.fire(
                                        'Deleted!',
                                        'Your file has been deleted.',
                                        'success'
                                        );
                                    // Fire.$emit('AfterCreate');
                                    this.loadUsers();
                                }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                         }
                    })
            },
          loadUsers(){
            this.$Progress.start();

            if(this.$gate.isAdmin()){
              axios.get("api/user").then(({ data }) => (this.users = data.data));
            }

            this.$Progress.finish();
          },
          
          createUser(){

              this.form.post('api/user')
              .then((response)=>{
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: response.data.message
                  });

                  this.$Progress.finish();
                  this.loadUsers();

              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          }

        },
        mounted() {
            console.log('User Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadUsers();
            this.$Progress.finish();
        }
    }
</script>
