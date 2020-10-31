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
                      <th>Slider Image</th>
                      <th>Slider Title</th>
                      <th>Slider Sub Title</th>
                      <th>Slider Url</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                      <tr v-for="(slider, index) in sliders.data" :key="slider.id">
                      <td>{{ index+1 }}</td>
                      <td>
                        <img v-if="slider.slider_image" :src="'upload/slider/'+slider.slider_image" style="width:60px;height:60px;">
                      </td>
                      <td>{{slider.slider_title}}</td>
                      <td>{{slider.slider_sub_title}}</td>
                      <td>{{slider.slider_url}}</td>
                      <td>
                        
                        <a
                        href="#"
                        @click="editModal(slider)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteSlider(slider.id)"
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
                  <pagination :data="sliders" @pagination-change-page="getResults"></pagination>
              </div>
            </div>
          
          </div>
        </div>

       
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="addNew" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" v-show="!editmode">Create New Slider</h5>
                    <h5 class="modal-title" v-show="editmode">Edit Slider</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <form @submit.prevent="editmode ? updateSlider() : createSlider()">
                    <div class="modal-body">

                        <div class="form-group">
                            <label>Slider Image <span style="color: #fb0606">*</span></label>
                            <div>
                              <input type="file" @v-model="form.slider_image" @change='browse_image' accept=".png, .jpg, .jpeg"  >
                              <input type="hidden" @v-model="form.hidden_image">
                            </div>
                            <div v-if="show_image !== null">
                                <img v-bind:src="show_image" style="width: 90px;height:90px;" />
                            </div>
                        </div>
                      
                        <div class="form-group">
                            <label>Slider Title <span style="color: #fb0606">*</span></label>
                            <input v-model="form.slider_title" type="text" name="slider_title" class="form-control" :class="{ 'is-invalid': form.errors.has('slider_title') }" placeholder="Enter slider Title" required>
                            <has-error :form="form" field="slider_title"></has-error>
                        </div>

                        <div class="form-group">
                            <label>Slider Sub Title</label>
                            <textarea v-model="form.slider_sub_title" name="slider_sub_title" class="form-control" placeholder="Enter slider Sub Title" style="resize:none;"></textarea>
                            <has-error :form="form" field="slider_sub_title"></has-error>
                        </div>

                       
                        <div class="form-group">
                            <label>Slider Url <span style="color: #fb0606">*</span></label>
                            <input v-model="form.slider_url" type="text" name="slider_url" class="form-control" :class="{ 'is-invalid': form.errors.has('slider_url') }" placeholder="Enter slider Url" required>
                            <has-error :form="form" field="slider_url"></has-error>
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
      components: {
          
        },
        data () {
            return {
                editmode: false,
                sliders : {},
                show_image: null,
                form: new Form({
                    id : '',
                    slider_image:'',
                    slider_title: '',
                    slider_sub_title:'',
                    slider_url: '',
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
                  this.form.slider_image = reader.result;
                  this.show_image = this.form.slider_image;
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
              let slider_image = (this.form.slider_image.length > 100) ? this.form.slider_image : this.form.slider_image;
              return slider_image;
          },*/

          getResults(page = 1) {

              this.$Progress.start();
              
              axios.get('api/slider?page=' + page).then(({ data }) => (this.sliders = data.data));

              this.$Progress.finish();
          },

          loadSliders(){
              axios.get("api/slider").then(({ data }) => (this.sliders = data.data)
                 
              );
          },

          editModal(slider){
              this.editmode = true;
              this.form.reset();
              $('#addNew').modal('show');
              
              slider.hidden_image = slider.slider_image;
              if(slider.slider_image !== null){
                this.show_image = "upload/slider/"+ slider.slider_image;
              }
              else{
                this.show_image = null;
              }

              this.form.fill(slider);
          },

          newModal(){
              this.editmode = false;
              this.form.reset();
              this.show_image = null;
              $('#addNew').modal('show');
          },

          createSlider(){
              this.$Progress.start();
              this.form.post('api/slider')
              .then((data)=>{
                if(data.data.success){
                  $('#addNew').modal('hide');

                  Toast.fire({
                        icon: 'success',
                        title: data.data.message
                    });
                  this.$Progress.finish();
                  this.loadSliders();

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

          updateSlider(event){
              this.$Progress.start();
              //console.log(this.form.slider_image);
              //event.preventDefault();
              this.form.put('api/slider/'+this.form.id)
              .then((response) => {
                  $('#addNew').modal('hide');
                  Toast.fire({
                    icon: 'success',
                    title: response.data.message
                  });
                  this.$Progress.finish();
                  this.loadSliders();
              })
              .catch(() => {
                  this.$Progress.fail();
              });

          },

          deleteSlider(id){
              Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  showCancelButton: true,
                  confirmButtonColor: '#d33',
                  cancelButtonColor: '#3085d6',
                  confirmButtonText: 'Yes, delete it!'
                  }).then((result) => {

                        if (result.value) {
                              this.form.delete('api/slider/'+id).then(()=>{
                                      Swal.fire(
                                      'Deleted!',
                                      'Your file has been deleted.',
                                      'success'
                                      );
                                  this.loadSliders();
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
            this.loadSliders();
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
