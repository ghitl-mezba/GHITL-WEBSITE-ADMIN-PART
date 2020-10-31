<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-list"></i> Footer List</h3>

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
                    <th>Title</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(footer, index) in footers.data" :key="footer.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ footer.title }}</td>
                    <td class="text-center">
                      <a
                        href="#"
                        @click="editModal(footer)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteFooter(footer.id)"
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
                :data="footers"
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
              <h5 class="modal-title" v-show="!editmode">Create New footer</h5>
              <h5 class="modal-title" v-show="editmode">Update footer</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateFooter() : createFooter()">
              <div class="modal-body">
                <div class="form-group">
                  <label
                    >Footer Name <span style="color: #fb0606">*</span></label
                  >
                  <input
                    v-model="form.title"
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('name') }"
                    maxlength="250"
                    placeholder="Enter footer name"
                    required
                  />
                  <has-error :form="form" field="name"></has-error>
                </div>
              </div>
              <div
                v-for="(input, k) in form.inputs"
                :key="k"
                class="col-md-9"
                style="border-bottom: 1px solid #ddd; padding-bottom: 10px"
              >
                <div class="form-group">
                  <label>Title <span style="color: #fb0606">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="input.title"
                    placeholder="Enter title"
                    required
                  />
                </div>
                <div class="form-group">
                  <label>URL <span style="color: #fb0606">*</span></label>
                  <input
                    type="text"
                    class="form-control"
                    v-model="input.url"
                    placeholder="Enter URL"
                    required
                  />
                </div>
                <span>
                  <button
                    type="button"
                    @click="remove(k, input.id ? input.id : null)"
                    class="btn btn-danger"
                    v-show="k || (!k && form.inputs.length > 1)"
                  >
                  <i class="fa fa-minus"></i> Remove
                  </button>
                  <button
                    type="button"
                    @click="add(k)"
                    class="btn btn-success"
                    v-show="k == form.inputs.length - 1"
                  >
                    <i class="fa fa-plus"></i> Add more
                  </button>
                </span>
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
  data() {
    return {
      editmode: false,
      footers: {},
      form: new Form({
        id: "",
        title: "",
        inputs: [
          {
            title: "",
            url: "",
          },
        ],
      }),
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/footer?page=" + page)
        .then(({ data }) => (this.footers = data.data));

      this.$Progress.finish();
    },
    updateFooter() {
      this.$Progress.start();
      this.form
        .put("/api/footer/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadFooter();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    editModal(footer) {
      this.editmode = true;
      this.form.reset();
      axios
        .get("api/footer/" + footer.id)
        .then(({ data }) => (this.form.inputs = data.data));
      $("#addNew").modal("show");
      console.log(footer);
      this.form.fill(footer);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },

    loadFooter() {
      if (this.$gate.isAdmin()) {
        axios.get("/api/footer").then(({ data }) => (this.footers = data.data));
      }
    },

    add() {
      this.form.inputs.push({
        title: "",
        url: "",
      });
      console.log(this.form.inputs);
    },

    remove(index, id) {
      this.form.inputs.splice(index, 1);
      if (id) {
        axios
          .post("api/footer/remove", { id: id })
          .then(({ data }) => console.log("delete"));
      }
    },

    createFooter() {
      this.form
        .post("/api/footer")
        .then((response) => {
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: response.data.message,
          });

          this.$Progress.finish();
          this.loadFooter();
        })
        .catch(() => {
          Toast.fire({
            icon: "error",
            title: "Some error occured! Please try again",
          });
        });
    },
    deleteFooter(id) {
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
            .delete("api/footer/" + id)
            .then(() => {
              Swal.fire("Deleted!", "Data has been deleted.", "success");
              this.loadFooter();
            })
            .catch((data) => {
              Swal.fire("Failed!", data.message, "warning");
            });
        }
      });
    },
  },
  mounted() {
    console.log("Component mounted.");
  },
  created() {
    this.$Progress.start();
    this.loadFooter();
    this.$Progress.finish();
  },
};
</script>
