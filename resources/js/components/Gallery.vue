<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title">
                <i class="fa fa-list"></i> Project List
              </h3>
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
                    <th>Project Image</th>
                    <th>Project Title</th>
                    <th>Project Description</th>
                    <th>Project Price</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(gallery, index) in galleries.data"
                    :key="gallery.id"
                  >
                    <td>{{ index + 1 }}</td>
                    <td>
                      <img
                        v-if="gallery.gallery_image"
                        :src="'upload/gallery/' + gallery.gallery_image"
                        style="width: 60px; height: 60px"
                      />
                    </td>
                    <td>{{ gallery.gallery_title }}</td>
                    <td>{{ gallery.gallery_description }}</td>
                    <td>{{ gallery.gallery_price }}</td>
                    <td>
                      <a
                        href="#"
                        @click="editModal(gallery)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteGallery(gallery.id)"
                        class="btn btn-danger"
                      >
                        <i class="fa fa-trash"></i>
                        Delete
                      </a>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div class="card-footer" style="background-color: #fff">
              <pagination
                :data="galleries"
                @pagination-change-page="getResults"
              ></pagination>
            </div>
          </div>
        </div>
      </div>

      <div
        class="modal fade"
        id="addNew"
        tabindex="-1"
        role="dialog"
        aria-labelledby="addNew"
        aria-hidden="true"
      >
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header bg-primary">
              <h5 class="modal-title" v-show="!editmode">Create New Project</h5>
              <h5 class="modal-title" v-show="editmode">Edit Project</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateGallery() : createGallery()">
              <div class="modal-body">
                <div class="form-group">
                  <label
                    >Project Image <span style="color: #fb0606">*</span></label
                  >
                  <div>
                    <input
                      type="file"
                      @v-model="form.gallery_image"
                      @change="browse_image"
                      accept=".png, .jpg, .jpeg"
                    />
                    <input type="hidden" @v-model="form.hidden_image" />
                  </div>
                  <div v-if="show_image !== null">
                    <img
                      v-bind:src="show_image"
                      style="width: 90px; height: 90px"
                    />
                  </div>
                </div>

                <div class="form-group">
                  <label
                    >Project Title <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.gallery_title"
                    type="text"
                    name="gallery_title"
                    class="form-control"
                    placeholder="Enter gallery Title"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Project Description <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.gallery_description"
                    type="text"
                    name="gallery_description"
                    class="form-control"
                    placeholder="Enter gallery description"
                  />
                </div>

                <div class="form-group">
                  <label>Project Price <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.gallery_price"
                    type="text"
                    name="gallery_price"
                    class="form-control"
                    placeholder="Enter gallery price"
                  />
                </div>


              </div>

              <div class="modal-footer" style="background-color: aliceblue">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                >
                  <i class="fa fa-times"></i> Close
                </button>
                <button v-show="editmode" type="submit" class="btn btn-success">
                  <i class="fa fa-edit"></i> Update
                </button>
                <button
                  v-show="!editmode"
                  type="submit"
                  class="btn btn-primary"
                >
                  <i class="fa fa-save"></i> Save
                </button>
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
  components: {},
  data() {
    return {
      editmode: false,
      galleries: {},
      show_image: null,
      form: new Form({
        id: "",
        gallery_image: "",
        gallery_title: "",
        gallery_description: "",
        gallery_price: "",
        hidden_image: "",
      }),
    };
  },
  methods: {
    browse_image(e) {
      let file = e.target.files[0];
      let reader = new FileReader();

      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.gallery_image = reader.result;
          this.show_image = this.form.gallery_image;
          this.hidden_image = "";
        };
        reader.readAsDataURL(file);
      } else {
        Toast.fire({
          icon: "error",
          title: "File size can not be bigger than 2 MB",
        });
      }
    },

    /*show_image(){
              let gallery_image = (this.form.gallery_image.length > 100) ? this.form.gallery_image : this.form.gallery_image;
              return gallery_image;
          },*/

    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("api/gallery?page=" + page)
        .then(({ data }) => (this.galleries = data.data));

      this.$Progress.finish();
    },

    loadGalleries() {
      axios.get("api/gallery").then(({ data }) => (this.galleries = data.data));
    },

    editModal(gallery) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");

      gallery.hidden_image = gallery.gallery_image;
      if (gallery.gallery_image !== null) {
        this.show_image = "upload/gallery/" + gallery.gallery_image;
      } else {
        this.show_image = null;
      }

      this.form.fill(gallery);
    },

    newModal() {
      this.editmode = false;
      this.form.reset();
      this.show_image = null;
      $("#addNew").modal("show");
    },

    createGallery() {
      this.$Progress.start();
      this.form
        .post("api/gallery")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadGalleries();
          } else {
            Toast.fire({
              icon: "error",
              title: "Some error occured! Please try again",
            });

            this.$Progress.failed();
          }
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },

    updateGallery(event) {
      this.$Progress.start();
      //console.log(this.form.gallery_image);
      //event.preventDefault();
      this.form
        .put("api/gallery/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadGalleries();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteGallery(id) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete it!",
      }).then((result) => {
        if (result.value) {
          this.form
            .delete("api/gallery/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              this.loadGalleries();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
  },

  mounted() {
    $("#addNew").on("hidden.bs.modal", () => {
      this.show_image = null;
    });
  },
  created() {
    this.$Progress.start();
    this.loadGalleries();
    this.$Progress.finish();
  },

  filters: {
    truncate: function (text, length, suffix) {
      return text.substring(0, length) + suffix;
    },
  },

  computed: {},
};
</script>
