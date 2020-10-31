<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-primary">
              <h3 class="card-title">
                <i class="fa fa-list"></i> Video List
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
                    <th>Video Image</th>
                    <th>Video Title</th>
                    <th>Video Url</th>
                    <th>Video Target</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(video, index) in videos.data" :key="video.id">
                    <td>{{ index + 1 }}</td>
                    <td>
                      <img
                        v-if="video.video_image"
                        :src="'upload/video/' + video.video_image"
                        style="width: 60px; height: 60px"
                      />
                    </td>
                    <td>{{ video.video_title }}</td>
                    <td>{{ video.video_url }}</td>
                    <td class="text-capitalize">
                        <span v-if="video.video_target">
                            {{ video.video_target.slice(1) }}
                      </span>
                    </td>
                    <td>
                      <a
                        href="#"
                        @click="editModal(video)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteVideo(video.id)"
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
                :data="videos"
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
              <h5 class="modal-title" v-show="!editmode">Create New Video</h5>
              <h5 class="modal-title" v-show="editmode">Edit Video</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateVideo() : createVideo()">
              <div class="modal-body">

                <div class="form-group">
                  <label
                    >Video Image <span style="color: #fb0606">*</span></label
                  >
                  <div>
                    <input
                      type="file"
                      @v-model="form.video_image"
                      @change="browse_image"
                      accept=".png, .jpg, .jpeg"
                      required
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
                    >Video Title <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.video_title"
                    type="text"
                    name="video_title"
                    class="form-control"
                    placeholder="Enter video Title"
                    required
                  />
                </div>

                <div class="form-group">
                  <label
                    >Video Url <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.video_url"
                    type="text"
                    name="video_url"
                    class="form-control"
                    placeholder="Enter video url"
                    required
                  />
                </div>

                <div class="form-group">
                  <label>Video Target</label>
                  <select
                    id="video_target"
                    v-model="form.video_target"
                    name="video_target"
                    class="form-control"
                  >
                    <option value="">Select Video Target</option>
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
      videos: {},
      show_image: null,
      form: new Form({
        id: "",
        video_image: "",
        video_title: "",
        video_url: "",
        video_target: "",
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
          this.form.video_image = reader.result;
          this.show_image = this.form.video_image;
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
              let video_image = (this.form.video_image.length > 100) ? this.form.video_image : this.form.video_image;
              return video_image;
          },*/

    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("api/video?page=" + page)
        .then(({ data }) => (this.videos = data.data));

      this.$Progress.finish();
    },

    loadVideos() {
      axios.get("api/video").then(({ data }) => (this.videos = data.data));
    },

    editModal(video) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");

      video.hidden_image = video.video_image;
      if (video.video_image !== null) {
        this.show_image = "upload/video/" + video.video_image;
      } else {
        this.show_image = null;
      }

      this.form.fill(video);
    },

    newModal() {
      this.editmode = false;
      this.form.reset();
      this.show_image = null;
      $("#addNew").modal("show");
    },

    createVideo() {
      this.$Progress.start();
      this.form
        .post("api/video")
        .then((data) => {
          if (data.data.success) {
            $("#addNew").modal("hide");

            Toast.fire({
              icon: "success",
              title: data.data.message,
            });
            this.$Progress.finish();
            this.loadVideos();
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

    updateVideo(event) {
      this.$Progress.start();
      //console.log(this.form.video_image);
      //event.preventDefault();
      this.form
        .put("api/video/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadVideos();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },

    deleteVideo(id) {
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
            .delete("api/video/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Your file has been deleted.", "success");
              this.loadVideos();
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
    this.loadVideos();
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
