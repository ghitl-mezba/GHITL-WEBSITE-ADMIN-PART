<template>
  <section class="content">
    <div class="container-fluid">
        <div class="row">

          <div class="col-12">
        
            <div class="card">
              <div class="card-header bg-primary">
                <h3 class="card-title"><i class="fa fa-list"></i> Product List</h3>

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
                      <th>Category</th>
                      <th>Sub Category</th>
                      <th>Product Name</th>
                      <th>Price</th>
                      <th>Discount Price</th>
                      <th>Size</th>
                      <th>Tag</th>
                      <th>Image</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr v-for="(product, index) in products.data" :key="product.id">
                      <td>{{ index+1 }}</td>
                      <td>{{product.category.name}}</td>
                      <td>{{product.sub_category.sub_category_name}}</td>
                      <td>{{product.name}}</td>
                      <td>{{product.price}}</td>
                      <td>{{product.discount_price}}</td>
                      <td>{{ product.sizes.map(a => a.text).join(', ') }}</td>
                      <td>{{ product.tags.map(a => a.text).join(', ') }}</td>
                      <td>
                        <img v-if="product.photo" :src="'upload/product-primary/'+product.photo" style="width:60px;height:60px;">
                      </td>
                      <td>
                        
                        <a
                        href="#"
                        @click="editModal(product)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteProduct(product.id)"
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
                  <pagination :data="products" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
          
          </div>
        </div>

       
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" v-show="!editmode">Create New Product</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateProduct() : createProduct()">
                    <div class="modal-body">

                        <div class="row form-group">
                            <div class="col-md-2">
                              <label>Category <span style="color: #fb0606">*</span></label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name ="category_id" v-model="form.category_id" @change="getSubCategory" required>
                                  <option value="">Select Category</option>  
                                  <option 
                                      v-for="(cat,index) in categories" :key="index"
                                      :value="index"
                                      :selected="index == form.category_id">{{ cat }}</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                               <label>Sub Category <span style="color: #fb0606">*</span></label>
                            </div>
                            <div class="col-md-4">
                                <select class="form-control" name="sub_category_id" v-model="form.sub_category_id" required>
                                <option value="">Select Sub Category</option>  
                                <option 
                                    v-for="(cat,index) in subCategories" :key="index"
                                    :value="cat.id"
                                    :selected="index == form.sub_category_id">{{ cat.sub_category_name }}</option>
                              </select>
                            </div>

                        </div>

                        <div class="form-group row">
                           <div class="col-md-2">
                              <label>Product Name <span style="color: #fb0606">*</span></label>
                           </div>
                           <div class="col-md-4">
                               <input v-model="form.name" type="text" name="name" class="form-control" placeholder="Enter product name" required>
                           </div>
                           <div class="col-md-2">
                              <label>Price <span style="color: #fb0606">*</span></label>
                            </div> 
                            <div class="col-md-4">
                                <input v-model="form.price" type="text" name="price" class="form-control" placeholder="Enter product price" required>
                            </div>
                          
                        </div>

                        <div class="form-group row" style="height:100px;" >
                            <div class="col-md-2">
                              <label>Discount Price</label> 
                            </div> 
                            <div class="col-md-4">
                              <input v-model="form.discount_price" type="text" name="discount_price" class="form-control" placeholder="Enter product discount price">
                            </div>

                            <div class="col-md-2">
                              <label>Image</label>
                            </div>  
                            <div class="col-md-2">
                                <input type="file" @v-model="form.photo" @change='browse_image' accept=".png, .jpg, .jpeg" >
                                <input type="hidden" @v-model="form.hidden_image">
                            </div>
                            <div class="col-md-2" v-if="show_image !== null">
                                <img v-bind:src="show_image" style="width: 90px;height:90px;border:1px solid #ddd;padding:2px;" />
                            </div>
                        </div>
                        <hr style="padding-bottom:20px;" />

                         <div class="form-group row">
                            <div class="col-md-2">
                              <label>Tags</label>
                            </div> 
                              <div class="col-md-4">
                              <vue-tags-input
                                v-model="form.tag"
                                :tags="form.tags"
                                :autocomplete-items="filteredTags"
                                :add-only-from-autocomplete="true"
                                @tags-changed="newTags => form.tags = newTags" placeholder="Add New Tag"/>
                            </div>
                            <div class="col-md-2">
                              <label>Sizes</label>
                            </div>  
                            <div class="col-md-4">
                               <vue-tags-input
                                v-model="form.size"
                                :tags="form.sizes"
                                :autocomplete-items="filteredSizes"
                                :add-only-from-autocomplete="true"
                                @tags-changed="newTags => form.sizes = newTags" placeholder="Add New Size"/>
                            </div> 

                         </div>

                         <div class="row form-group">
                            <div class="col-md-2">
                              <label>Description</label>
                            </div>
                             <div class="col-md-10">
                               <ckeditor :editor="editor" v-model="form.description"></ckeditor>
                            </div>
                         </div>

                          <div class="row form-group">
                            <div class="col-md-2">
                              <label>Specification</label>
                            </div>
                             <div class="col-md-10">
                               <ckeditor :editor="editor" v-model="form.specification"></ckeditor>
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
    import VueTagsInput from '@johmun/vue-tags-input';
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';


    export default {
      components: {
          VueTagsInput,
        },
        data () {
            return {
                editor: ClassicEditor,
                editmode: false,
                products : {},
                show_image: null,
                form: new Form({
                    id : '',
                    category_id : '',
                    sub_category_id: '',
                    name: '',
                    description:'',
                    specification: '',
                    price: '',
                    discount_price: '',
                    tags:  [],
                    sizes:  [],
                    photo:'',
                    hidden_image: ''
                }),
                categories: [],
                subCategories: [],
                tag:  '',
                size: '',
                autocompleteTags: [],
                autocompleteSizes: [],
            }
        },
        methods: {
        browse_image(e){
            let file = e.target.files[0];
            let reader = new FileReader();  

            if(file['size'] < 2111775)
            {
                reader.onloadend = (file) => {
                  this.form.photo = reader.result;
                  this.show_image = this.form.photo;
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
            
          /*show_image(){
              let photo = (this.form.photo.length > 100) ? this.form.photo : this.form.photo;
              return photo;
          },*/

          getResults(page = 1) {

              this.$Progress.start();
              
              axios.get('api/product?page=' + page).then(({ data }) => (this.products = data.data));

              this.$Progress.finish();
          },

          loadProducts(){
              axios.get("api/product").then(({ data }) => (this.products = data.data)
                 
              );
          },

          loadCategories(){
              axios.get("/api/category/list").then(({ data }) => (this.categories = data.data));
          },

          getSubCategory(event) {
            this.form.sub_category_id = '';
            axios.get('/api/product/sub_category',{
                  params: {
                      category_id: event.target.value
                  }
              }).then(function(data){
                  this.subCategories = data.data;
              }.bind(this));
          },

          loadTags(){
              axios.get("/api/tag/list").then(response => {
                  this.autocompleteTags = response.data.data.map(a => {
                      return { text: a.name, id: a.id };
                  });
              }).catch(() => console.warn('Oh. Something went wrong'));
          },

          loadSizes(){
              axios.get("/api/size/list").then(response => {
                  this.autocompleteSizes = response.data.data.map(a => {
                      return { text: a.name, id: a.id };
                  });
              }).catch(() => console.warn('Oh. Something went wrong'));
          },

          editModal(product){
            
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              
              product.hidden_image = product.photo;
              if(product.photo !== null){
                this.show_image = "upload/product-primary/"+ product.photo;
              }
              else{
                this.show_image = null;
              }


              axios.get('/api/product/sub_category',{
                  params: {
                      category_id: product.category_id
                  }}).then(function(data){this.subCategories = data.data;}.bind(this));

              this.form.fill(product);
          },

          newModal(){
              this.editmode = false;
              this.form.reset();
              this.show_image = null;
              $('#addNew').modal('show');
          },

          createProduct(){
              this.$Progress.start();
              this.form.post('api/product')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadProducts();

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

          updateProduct(event){
              this.$Progress.start();
              //console.log(this.form.photo);
              //event.preventDefault();
              this.form.put('api/product/'+this.form.id)
              .then((response) => {
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                  this.loadProducts();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },

          deleteProduct(id){
              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                        if (result.value) {
                              this.form.delete('api/product/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  this.loadProducts();
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
            this.loadProducts();
            this.loadCategories();
            this.loadTags();
            this.loadSizes();
            this.$Progress.finish();
        },

        filters: {
            truncate: function (text, length, suffix) {
                return text.substring(0, length) + suffix;
            },
        },

        computed: {
          filteredTags() {
            return this.autocompleteTags.filter(i => {
              return i.text.toLowerCase().indexOf(this.tag.toLowerCase()) !== -1;
            });
          },
          
          filteredSizes() {
            return this.autocompleteSizes.filter(i => {
              return i.text.toLowerCase().indexOf(this.size.toLowerCase()) !== -1;
            });
          },
        },
    }
</script>
