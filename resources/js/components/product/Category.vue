<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-list"></i> Category List</h3>

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
                      <th>Category Name</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(category, index) in categories.data" :key="category.id">
                      <td>{{ index+1 }}</td>
                      <td class="text-capitalize">{{category.name}}</td>
                      <td class="text-center">
                       <a href="#" @click="editModal(category)" class="btn btn-success">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div class="card-footer" style="background-color: #fff;">
                  <pagination :data="categories" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Create New Category</h5>
                    <h5 class="modal-title" v-show="editmode">Update Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

              
                <form @submit.prevent="editmode ? updateCategory() : createCategory()">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Category Name <span style="color: #fb0606">*</span></label>
                            <input v-model="form.name" type="text" name="name" class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" maxlength="250" placeholder="Enter category name" required>
                            <has-error :form="form" field="name"></has-error>
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
                categories : {},
                form: new Form({
                    id : '',
                    name: '',
                })
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/category?page=' + page).then(({ data }) => (this.categories = data.data));

                  this.$Progress.finish();
            },
            updateCategory(){
                this.$Progress.start();
                this.form.put('/api/category/'+this.form.id)
                .then((response) => {
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadCategories();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(category){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(category);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },

            loadCategories(){
                if(this.$gate.isAdmin()){
                    axios.get("/api/category").then(({ data }) => (this.categories = data.data));
                }
            },
            
            createCategory(){

                this.form.post('/api/category')
                .then((response)=>{
                    $('#addNew').modal('hide');

                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.loadCategories();
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
            console.log('Component mounted.')
        },
        created() {

            this.$Progress.start();
            this.loadCategories();
            this.$Progress.finish();
        }
    }
</script>
