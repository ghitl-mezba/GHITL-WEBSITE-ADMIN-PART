<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card" v-if="$gate.isAdmin()">
            <div class="card-header bg-primary">
              <h3 class="card-title"><i class="fa fa-list"></i> FAQ List</h3>

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
                    <th>Question</th>
                    <th>Answer</th>
                    <th class="text-center">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(faq, index) in faqs.data" :key="faq.id">
                    <td>{{ index + 1 }}</td>
                    <td class="text-capitalize">{{ faq.question }}</td>
                    <td class="text-capitalize">{{ faq.answer }}</td>
                    <td class="text-center">
                      <a
                        href="#"
                        @click="editModal(faq)"
                        class="btn btn-success"
                      >
                        <i class="fa fa-edit"></i> Edit
                      </a>
                      <a
                        href="#"
                        @click="deleteFaq(faq.id)"
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
                :data="faqs"
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
              <h5 class="modal-title" v-show="!editmode">Create New Faq</h5>
              <h5 class="modal-title" v-show="editmode">Update Faq</h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
              >
                <span aria-hidden="true">&times;</span>
              </button>
            </div>

            <form @submit.prevent="editmode ? updateFaq() : createFaq()">
              <div class="modal-body">

                <div class="form-group">
                  <label>Question <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.question"
                    type="text"
                    name="question"
                    required
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('question') }"
                    maxlength="250"
                    placeholder="Enter question"
                  />
                  <has-error :form="form" field="question"></has-error>
                </div>
                 <div class="form-group">
                  <label>Answer <span style="color: #fb0606">*</span></label>
                  <input
                    v-model="form.answer"
                    type="text"
                    name="answer"
                    required
                    class="form-control"
                    :class="{ 'is-invalid': form.errors.has('answer') }"
                    maxlength="250"
                    placeholder="Enter answer"
                  />
                  <has-error :form="form" field="answer"></has-error>
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
      faqs: {},
      form: new Form({
        id: "",
        question: "",
        answer: "",
      }),
    };
  },
  methods: {
    getResults(page = 1) {
      this.$Progress.start();

      axios
        .get("/api/faq?page=" + page)
        .then(({ data }) => (this.faqs = data.data));

      this.$Progress.finish();
    },
    updateFaq() {
      this.$Progress.start();
      this.form
        .put("/api/faq/" + this.form.id)
        .then((response) => {
          $("#addNew").modal("hide");
          Toast.fire({
            icon: "success",
            title: response.data.message,
          });
          this.$Progress.finish();
          this.loadFaqs();
        })
        .catch(() => {
          this.$Progress.fail();
        });
    },
    deleteFaq(id) {
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, delete it!'
          }).then((result) => {
                if (result.value) {
                      this.form.delete('api/faq/'+id).then(()=>{
                              Swal.fire(
                              'Deleted!',
                              'Your file has been deleted.',
                              'success'
                              );
                          this.loadFaqs();
                      }).catch((data)=> {
                          Swal.fire("Failed!", data.message, "warning");
                      });
                }
          })
    },
    editModal(faq) {
      this.editmode = true;
      this.form.reset();
      $("#addNew").modal("show");
      this.form.fill(faq);
    },
    newModal() {
      this.editmode = false;
      this.form.reset();
      $("#addNew").modal("show");
    },

    loadFaqs() {
      if (this.$gate.isAdmin()) {
        axios.get("/api/faq").then(({ data }) => (this.faqs = data.data));
      }
    },

    createFaq() {
      this.form
        .post("/api/faq")
        .then((response) => {
          $("#addNew").modal("hide");

          Toast.fire({
            icon: "success",
            title: response.data.message,
          });

          this.$Progress.finish();
          this.loadFaqs();
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
    this.loadFaqs();
    this.$Progress.finish();
  },
};
</script>
