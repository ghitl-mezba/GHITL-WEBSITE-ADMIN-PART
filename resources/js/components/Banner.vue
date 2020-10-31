<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title">
                <i class="fa fa-list"></i> Banner List
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
                    <th>Banner Show</th>
                    <th>Banner Image</th>
                    <th>Banner Title</th>
                    <th>Banner Sub Title</th>
                    <th>Banner Button Name</th>
                    <th>Banner Url</th>
                    <th>Banner Target</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(banner, index) in banners.data" :key="banner.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ banner.banner_id }}</td>
                    <td>
                      <img
                        v-if="banner.banner_image"
                        :src="'upload/banner/' + banner.banner_image"
                        style="width: 60px; height: 60px"
                      />
                    </td>
                    <td>{{ banner.banner_title }}</td>
                    <td>{{ banner.banner_sub_title }}</td>
                    <td>{{ banner.banner_button_name }}</td>
                    <td>{{ banner.banner_url }}</td>
                    <td class="text-capitalize">
                    <span v-if="video.video_target">
                      {{ banner.banner_target.slice(1) }}
                    </span>
                    </td>
                    <td>
                      <a
                        href="#"
                        @click="editModal(banner)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteBanner(banner.id)"
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
                :data="banners"
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
              <h5 class="modal-title" v-show="!editmode">Create New Banner</h5>
              <h5 class="modal-title" v-show="editmode">Edit Banner</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateBanner() : createBanner()">
              <div class="modal-body">
                <div class="form-group">
                  <label>Banner Show</label>
                  <select
                    id="banner_id"
                    v-model="form.banner_id"
                    name="banner_id"
                    class="form-control"
                  >
                    <option value="">Sellect Banner Show</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                  </select>
                </div>

                <div class="form-group">
                  <label
                    >Banner Image <span style="color: #fb0606">*</span></label
                  >
                  <div>
                    <input
                      type="file"
                      @v-model="form.banner_image"
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
                    >Banner Title <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.banner_title"
                    type="text"
                    name="banner_title"
                    class="form-control"
                    placeholder="Enter banner Title"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Banner Sub Title</label>
                  <textarea
                    v-model="form.banner_sub_title"
                    name="banner_sub_title"
                    class="form-control"
                    placeholder="Enter banner Sub Title"
                    style="resize: none"
                  ></textarea>
                </div>

                <div class="form-group">
                  <label
                    >Banner Button Name
                    <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.banner_button_name"
                    type="text"
                    name="banner_button_name"
                    class="form-control"
                    placeholder="Enter banner Button Name"
                    required
                  />
                </div>

                <div class="form-group">
                  <label
                    >Banner Url <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.banner_url"
                    type="text"
                    name="banner_url"
                    class="form-control"
                    placeholder="Enter banner url"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Banner Target</label>
                  <select
                    id="banner_target"
                    v-model="form.banner_target"
                    name="banner_target"
                    class="form-control"
                  >
                    <option value="">Select Banner Target</option>
                    <option value="_blank">Blank</option>
                    <option value="_self">Self</option>
                    <option value="_parent">Parent</option>
                    <option value="_top">Top</option>
                  </select>
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
      banners: {},
      show_image: null,
      form: new Form({
        id: "",
        banner_id: "",
        banner_image: "",
        banner_title: "",
        banner_sub_title: "",
        banner_button_name: "",
        banner_url: "",
        banner_target: "",
        hidden_image: "",
        hidden_id: "",
      }),
    };
  },
  methods: {
    browse_image(e) {
      let file = e.target.files[0];
      let reader = new FileReader();

      if (file["size"] < 2111775) {
        reader.onloadend = (file) => {
          this.form.banner_image = reader.result;
          this.show_image = this.form.banner_image;
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
              let banner_image = (this.form.banner_image.length > 100) ? this.form.banner_image : this.form.banner_image;
              return banner_image;
          },*/

    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("api/banner?page=" + page)
        .then(({ data }) => (this.banners = data.data));

      this.$Progress.finish();
    },

    loadBanners() {
      axios.get("api/banner").then(({ data }) => (this.banners = data.data));
    },

    editModal(banner) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");

      banner.hidden_id=banner.banner_id;

      banner.hidden_image = banner.banner_image;
      if (banner.banner_image !== null) {
        this.show_image = "upload/banner/" + banner.banner_image;
      } else {
        this.show_image = null;
      }

      this.form.fill(banner);
    },

    newModal() {
      this.editmode = false;
      this.form.reset();
      this.show_image = null;
      $("#addNew").modal("show");
    },

    createBanner() {
      this.$Progress.start();
      this.form
        .post("api/banner")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadBanners();
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

    updateBanner(event) {
      this.$Progress.start();
      //console.log(this.form.banner_image);
      //event.preventDefault();
      this.form
        .put("api/banner/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadBanners();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteBanner(id) {
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
            .delete("api/banner/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              this.loadBanners();
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
    this.loadBanners();
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
