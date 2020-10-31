<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-list"></i> Social Link List</h3>

              <div class="card-tools">
                <button
                  type="button"
                  class="btn btn-success"
                  @click="newModal"
                >
                  <i class="fa fa-plus"></i>
                  Add New
                </button>
              </div>
            </div>

            <div
              class="card-body table-responsive table-borderd table-striped p-0"
            >
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>SL</th>
                    <th>Link Icon Name</th>
                    <th>Link Title</th>
                    <th>Link URL</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(social_link, index) in social_links.data" :key="social_link.id">
                    <td>{{ index + 1 }}</td>
                    <td class="text-capitalize">{{ social_link.link_icon_name }}</td>
                    <td class="text-capitalize">{{ social_link.link_title }}</td>
                    <td class="text-capitalize">{{ social_link.link_url }}</td>
                    <td class="text-center">
                      <a
                        href="#"
                        @click="editModal(social_link)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteSocialLink(social_link.id)"
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
              <pagination
                :data="social_links"
                @pagination-change-page="getResults"
              ></pagination>
            </div>
          </div>
        </div>
      </div>

      <div v-if="!$gate.isAdmin()">
        <not-found></not-found>
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
              <h5 class="modal-title" v-show="!editmode">Create New Social Link</h5>
              <h5 class="modal-title" v-show="editmode">Update Social Link</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateSocialLink() : createSocialLink()">
              <div class="modal-body">
                <div class="form-group">
                  <label>Link Icon Name <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.link_icon_name"
                    type="text"
                    name="link_icon_name"
                    required
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('link_icon_name') }"
                    maxlength="250"
                    placeholder="Enter link icon name"
                  />
                  <has-error :form="form" field="link_icon_name"></has-error>

                  <label>Link Title <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.link_title"
                    type="text"
                    name="link_title"
                    required
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('link_title') }"
                    maxlength="250"
                    placeholder="Enter link title"
                  />
                  <has-error :form="form" field="link_title"></has-error>

                  <label>Link URL </label>
                  <input
                    v-model="form.link_url"
                    type="text"
                    name="link_url"
                    class="form-control"
                    maxlength="250"
                    placeholder="Enter link URL"
                  />
                
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
  data() {
    return {
      editmode: false,
      social_links: {},
      form: new Form({
        id: "",
        link_icon_name: "",
        link_title: "",
        link_url: "",
      }),
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/social_link?page=" + page)
        .then(({ data }) => (this.social_links = data.data));

      this.$Progress.finish();
    },
    updateSocialLink() {
      this.$Progress.start();
      this.form
        .put("/api/social_link/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadSocialLinks();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    
    deleteSocialLink(id) {
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
                if (result.value) {
                      this.form.delete('api/social_link/'+id).then(()=>{
                              Swal.fire(
                              'Deleted!',
                              'Your file has been deleted.',
                              'success'
                              );
                          this.loadSocialLinks();
                      }).catch((data)=> {
                          Swal.fire("Failed!", data.message, "warning");
                      });
                }
          })
    },

    editModal(social_link) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(social_link);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },

    loadSocialLinks() {
      if (this.$gate.isAdmin()) {
        axios.get("/api/social_link").then(({ data }) => (this.social_links = data.data));
      }
    },

    createSocialLink() {
      console.log(this.form);
      this.form
        .post("/api/social_link")
        .then((response) => {
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: response.data.message,
          });

          this.$Progress.finish();
          this.loadSocialLinks();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
  },
 
  mounted() {
    console.log("Component mounted.");
  },
  created() {
    this.$Progress.start();
    this.loadSocialLinks();
    this.$Progress.finish();
  },
};
</script>
