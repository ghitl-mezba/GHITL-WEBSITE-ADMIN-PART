<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card" v-if="$gate.isAdmin()">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-list"></i> Sub Category List</h3>

                <div class="card-tools">
                  
                  <button type="button" class="btn btn-success" @click="newModal">
                      <i class="fa fa-plus"></i>
                      Add New
                  </button>
                </div>
              </div>
             
              <div class="card-body table-responsive table-borderd table-striped p-0">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>SL</th>
                      <th>Category Name</th>
                      <th>Sub Category Name</th>
                      <th class="text-center">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(sub_category, index) in sub_categories.data" :key="sub_category.id">
                      <td>{{ index+1 }}</td>
                       <td>{{sub_category.category.name}}</td>
                      <td class="text-capitalize">{{sub_category.sub_category_name}}</td>
                      <td class="text-center">
                         <a href="#" @click="editModal(sub_category)" class="btn btn-success">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
         
              <div class="card-footer" style="background-color: #fff;">
                  <pagination :data="sub_categories" @pagination-change-page="getResults"></pagination>
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
                    <h5 class="modal-title" v-show="!editmode">Create New Sub Category</h5>
                    <h5 class="modal-title" v-show="editmode">Update Sub Category</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateSubCategory() : createSubCategory()">
                    <div class="modal-body">

                        <div class="form-group">

                            <label>Category Name <span style="color: #fb0606">*</span></label>
                            <select class="form-control" v-model="form.category_id" required>
                              <option value="">Select Category</option>
                              <option 
                                  v-for="(cat,index) in categories" :key="index"
                                  :value="index"
                                  :selected="index == form.category_id">{{ cat }}</option>
                            </select>
                            <has-error :form="form" field="category_id"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Sub Category Name <span style="color: #fb0606">*</span></label>
                            <input v-model="form.sub_category_name" type="text" required name="sub_category_name" class="form-control" :class="{ 'is-invalid': form.errors.has('sub_category_name') }" placeholder="Enter sub category name" maxlength="250">
                            <has-error :form="form" field="sub_category_name"></has-error>
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
                sub_categories : {},
                form: new Form({
                    id : '',
                    category_id: '',
                    sub_category_name: '',
                }),
                categories: [],
            }
        },
        methods: {

            getResults(page = 1) {

                  this.$Progress.start();
                  
                  axios.get('/api/sub_category?page=' + page).then(({ data }) => (this.sub_categories = data.data));

                  this.$Progress.finish();
            },
            updateSubCategory(){
                this.$Progress.start();
                this.form.put('/api/sub_category/'+this.form.id)
                .then((response) => {
                    $('#addNew').modal('hide');
                    Toast.fire({
                      icon: 'success',
                      title: response.data.message
                    });
                    this.$Progress.finish();
                    this.loadSubCategories();
                })
                .catch(() => {
                    this.$Progress.fail();
                });

            },
            editModal(sub_category){
                this.editmode = true;
                this.form.reset();
                $('#addNew').modal('show');
                this.form.fill(sub_category);
            },
            newModal(){
                this.editmode = false;
                this.form.reset();
                $('#addNew').modal('show');
            },
            loadCategories(){
              axios.get("/api/category/list").then(({ data }) => (this.categories = data.data));
            },
            loadSubCategories(){
                if(this.$gate.isAdmin()){
                    axios.get("/api/sub_category").then(({ data }) => (this.sub_categories = data.data));
                }
            },
            
            createSubCategory(){

                this.form.post('/api/sub_category')
                .then((response)=>{
                    $('#addNew').modal('hide');

                    Toast.fire({
                            icon: 'success',
                            title: response.data.message
                    });

                    this.$Progress.finish();
                    this.loadSubCategories();
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
            this.loadCategories();
            this.$Progress.start();
            this.loadSubCategories();
            this.$Progress.finish();
        }
    }
</script>
