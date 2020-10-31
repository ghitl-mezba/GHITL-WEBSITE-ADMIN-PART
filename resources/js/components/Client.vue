<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-list"></i> Client List</h3>

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
                      <th>Client Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Address</th>
                      <th>URL</th>
                      <th>Target URL</th>
                      <th>Logo</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr v-for="(client, index) in clients.data" :key="client.id">
                      <td>{{ index+1 }}</td>
                      <td>{{client.client_name}}</td>
                      <td>{{client.client_email}}</td>
                      <td>{{client.client_phone}}</td>
                      <td>{{client.client_address}}</td>
                      <td>{{client.client_url}}</td>
                      <td class="text-capitalize">{{client.target_url.slice(1)}}</td>
                      <td>
                        <img v-if="client.client_logo" :src="'upload/client/'+client.client_logo" style="width:60px;height:60px;">
                      </td>
                      <td>
                        
                        <a
                        href="#"
                        @click="editModal(client)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteClient(client.id)"
                        class="btn btn-danger"
                      >
                        <i class="fa fa-trash"></i> Delete
                      </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
           
              <div class="card-footer" style="background-color: #fff">
                  <pagination :data="clients" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
          
          </div>
        </div>

       
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" v-show="!editmode">Create New Client</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Client</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateClient() : createClient()">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Client Name <span style="color: #fb0606">*</span></label>
                            <input v-model="form.client_name" type="text" name="client_name" class="form-control" :class="{ 'is-invalid': form.errors.has('client_name') }" placeholder="Enter client name" required>
                            <has-error :form="form" field="name"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Email</label>
                            <input v-model="form.client_email" type="text" name="client_email" class="form-control" placeholder="Enter client email">
                        </div>

                       <div class="form-group">
                            <label>Phone</label>
                            <input v-model="form.client_phone" type="text" name="client_phone" class="form-control" placeholder="Enter client phone">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <input v-model="form.client_address" type="text" name="client_address" class="form-control" placeholder="Enter client address">
                        </div>

                        
                        <div class="form-group">
                            <label>URL</label>
                            <input v-model="form.client_url" type="text" name="client_url" class="form-control" placeholder="Enter client URL">
                        </div>

                        <div class="form-group">
                            <label>Target URL</label>
                            <select id="target_url" v-model="form.target_url" name="target_url" class="form-control">
                            <option value="_blank">Blank</option>
                            <option value="_self">Self</option>
                            <option value="_parent">Parent</option>
                            <option value="_top">Top</option>
                          </select>
                        </div>

                       <div class="form-group">
                            <label>Image</label>
                            <div>
                              <input type="file" @v-model="form.client_logo" @change='browse_image' accept=".png, .jpg, .jpeg" >
                              <input type="hidden" @v-model="form.hidden_image">
                            </div>
                            <div v-if="show_image !== null">
                                <img v-bind:src="show_image" style="width: 90px;height:90px;" />
                            </div>
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
                clients : {},
                show_image: null,
                form: new Form({
                    id : '',
                    client_name : '',
                    client_email: '',
                    client_phone: '',
                    client_address:'',
                    client_logo: '',
                    client_url: '',
                    target_url: '',
                    hidden_image: ''
                }),
            }
        },
        methods: {
        browse_image(e){
            let file = e.target.files[0];
            let reader = new FileReader();  

            if(file['size'] < 2111775)
            {
                reader.onloadend = (file) => {
                  this.form.client_logo = reader.result;
                  this.show_image = this.form.client_logo;
                  this.hidden_image = "";
                }              
                reader.readAsDataURL(file);
            }else{
              Toast.fire({
                icon: 'error',
                title: "File size can not be bigger than 2 MB"
              });
            }
          },
           
          getResults(page = 1) {

              this.$Progress.start();
              
              axios.get('api/client?page=' + page).then(({ data }) => (this.clients = data.data));

              this.$Progress.finish();
          },

          loadClients(){
              axios.get("api/client").then(({ data }) => (this.clients = data.data)
                 
              );
          },

          
          editModal(client){
            
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              
              client.hidden_image = client.client_logo;
              if(client.client_logo !== null){
                this.show_image = "upload/client/"+ client.client_logo;
              }
              else{
                this.show_image = null;
              }

              this.form.fill(client);
          },

          newModal(){
              this.editmode = false;
              this.form.reset();
              this.show_image = null;
              $('#addNew').modal('show');
          },

          createClient(){
              this.$Progress.start();
              this.form.post('api/client')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadClients();

                } else {
                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });

                  this.$Progress.failed();
                }
              })
              .catch(()=>{

                  Toast.fire({
                      icon: 'error',
                      title: 'Some error occured! Please try again'
                  });
              })
          },

          updateClient(event){
              this.$Progress.start();
              //event.preventDefault();
              this.form.put('api/client/'+this.form.id)
              .then((response) => {
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                  this.loadClients();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },

          deleteClient(id){
              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                        if (result.value) {
                              this.form.delete('api/client/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  this.loadClients();
                              }).catch((data)=> {
                                  Swal.fire("Failed!", data.message, "warning");
                              });
                        }
                  })
          },

        },

        mounted() {
          
         $('#addNew').on('hidden.bs.modal', () => {
            this.show_image = null;
         })
           
        },
        created() {
            this.$Progress.start();
            this.loadClients();
            this.$Progress.finish();
        },

        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
        },

        computed: {
         
        },
    }
</script>
