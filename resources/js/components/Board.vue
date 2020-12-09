<template>
    <div class="board_block">
        <div class="width-ful">
            <button id="" @click="exportDB" class="ft-r">Export Database</button>
        </div>
        <div class="width-ful">
            <div class="layout">
                <div v-for="(listItem, index) in initial_data" :key="index" >
                    <div class="layout__list">
                        <div class="layout__list__title">
                            {{ listItem.title }}
                            <!-- listItem.id -->
                            <button id="" class="ft-r mrgn-2" @click="removeColumn(listItem.id)">X</button>
                        </div>
                        <div class="text-a-center taskAdd" v-on:click="handleAddTaskClicked(listItem)">
                            <span> +Add Task</span>
                        </div>
                        <draggable :list="listItem.tasks" group="tasks" ghost-class="ghost-card" @end="handleTaskMoved">
                            <div
                                v-for="(listItem, index) in listItem.tasks"
                                :key="index"
                            >
                                <div class="list__card" v-on:click="handleTaskClicked(listItem)">
                                    <label>{{ listItem.title }}</label>
                                </div>
                            </div>
                        </draggable>
                    </div>
                </div>
                <div class="layout__list">
                    <div class="layout__list__title pad-y-5" v-on:click="handleAddColClicked()">
                       <p class="mrgn-0">+ New Column</p>
                    </div>
                </div>
            </div>
        </div>

        <modal name="task-detail-modal" class="task_modal">
            <div class="width_ful">
                <div class="side_pad-2 pad-y-1">
                    <div class="dis_i_flex wid_ful">
                        <label class="width1-3">Title</label>
                        <input v-model="editTask.title" placeholder="Title" class="width2-3">
                    </div>
                    <div class="dis_i_flex wid_ful  pad-y-1">
                        <label class="width1-3">Description</label>
                        <textarea v-model="editTask.description" class="width2-3" rows="5"></textarea>
                    </div>
                    <div class="wid_ful">
                        <button @click="editTaskClicked()">Edit</button>
                        <button @click="hideTaskDetailModal()">Cancel</button>
                    </div>
                </div>
            </div>
        </modal>
        <modal name="add-task-modal" class="task_modal">
            <div class="width_ful">
                <div class="side_pad-2 pad-y-1">
                    <div class="dis_i_flex wid_ful">
                        <label class="width1-3">Title</label>
                        <input v-model="addTask.title" placeholder="Title" class="width2-3">
                    </div>
                    <div class="dis_i_flex wid_ful  pad-y-1">
                        <label class="width1-3">Description</label>
                        <textarea v-model="addTask.description" class="width2-3" rows="5"></textarea>
                    </div>
                    <div class="wid_ful">
                        <button @click="addTaskClicked()">Add</button>
                        <button @click="hideAddTaskModal()">Cancel</button>
                    </div>
                </div>
            </div>
        </modal>
        <modal name="add-col-modal" class="task_modal">
            <div class="width_ful">
                <div class="side_pad-2 pad-y-1">
                    <div class="dis_i_flex wid_ful">
                        <label class="width1-3">Column Title</label>
                        <input v-model="addColumn.title" placeholder="Title" class="width2-3">
                    </div>
                    
                    <div class="wid_ful">
                        <button @click="addColumnBtnClicked()">Add</button>
                        <button @click="hideAddColumnModal()">Cancel</button>
                    </div>
                </div>
            </div>
        </modal>
        
    </div>
</template>

<script>
import draggable from "vuedraggable";

export default {
	components: {
        draggable,
	},
	data() {
		return {
            initial_data:null,
            editTask:{
                title:null,
                description:null,
                id:null,
            },
            addTask:{
                title:null,
                description:null,
                column_id:null,
            },
            addColumn:{
                title:null,
            }
		};
	},
	methods: {
        hideTaskDetailModal(){
            this.editTask.id = this.editTask.title = this.editTask.description = null
            this.$modal.hide('task-detail-modal');
        },
        hideAddTaskModal(){
            this.addTask.column_id = this.addTask.title = this.addTask.description = null
            this.$modal.hide('add-task-modal');
        },
        hideAddColumnModal(){
            this.addColumn.title = null
            this.$modal.hide('add-col-modal');
        },
        editTaskClicked(){
            let payload = {'title':this.editTask.title, 'description':this.editTask.description}

            axios.put(`/tasks/${this.editTask.id}`, payload).
            then((response)=>{
                this.initial_data = response.data
            }).
            catch(err => {
                console.log(err.response);
            });
            this.hideTaskDetailModal()

        },
        addTaskClicked(){
            let newTask = {'title':this.addTask.title, 'description':this.addTask.description, 'column_id':this.addTask.column_id}
            axios
                .post("/tasks", newTask)
                .then(response => {
                    let newTaskAdded = response.data
                    const columnIndex = this.initial_data.findIndex(
                        column => column.id === newTaskAdded.column_id
                    );
                    this.initial_data[columnIndex].tasks.push(newTaskAdded);
                })
            this.hideAddTaskModal()
        },
        handleTaskMoved() {
            // Send the entire list of statuses to the server
            axios.put("/tasks/sync", {columns: this.initial_data}).catch(err => {
                console.log(err.response);
                
            });
        },
        exportDB(){
            axios.get("/dump").
            then(response => {
                var fileURL = window.URL.createObjectURL(new Blob([response.data]));
                var fileLink = document.createElement('a');
                fileLink.href = fileURL;
                fileLink.setAttribute('download', 'dump.sql');
                document.body.appendChild(fileLink);
                fileLink.click();
            })
            .catch(err => {
                console.log(err.response);
                
            });
        },
        handleTaskClicked(task){
            this.editTask.title = task.title
            this.editTask.description = task.description
            this.editTask.id = task.id
            this.$modal.show('task-detail-modal');
        },
        handleAddTaskClicked(column){
            this.addTask.column_id=column.id
            this.$modal.show('add-task-modal');
        },
        handleAddColClicked(event){
            
            this.$modal.show('add-col-modal');
        },
        addColumnBtnClicked(){
            let newColumn = {'title': this.addColumn.title}
            axios
                .post("/columns", newColumn)
                .then(response => {
                    this.initial_data = response.data
                })
            this.hideAddColumnModal()
        },
        removeColumn(colId){
            let data = {'id': colId}
            axios
                .delete(`/columns/${colId}`)
                .then(response => {
                    const columnIndex = this.initial_data.findIndex(
                        column => column.id === colId
                    );
                    this.initial_data.splice(columnIndex, 1);
                })
        }
    },
	computed: {
		dragOptions() {
			return {
				animation: 1,
				group: "description",
				ghostClass: "ghost"
			};
		}
	},
	mounted() {
    },
    created(){
        axios.get("/tasks").
        then(response => {
            this.initial_data = response.data
        })

    }
};
</script>

<style scoped>
.ghost-card {
  opacity: 0.5;
  background: #F7FAFC;
  border: 1px solid #4299e1;
}
</style>