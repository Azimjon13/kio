<template>
    <div class="app-container">
        
        <h4>
            Справочник Тепловозов
            <br>
            <el-input v-model="locomotivePagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="locomotivePagination.serial_number" placeholder="Введите Серийный №" style="width: 250px;"></el-input>
            <el-input v-model="locomotivePagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadLocomotives">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-locomotive'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddLocomotive">Добавить Тепловоз</el-button>
        </h4>
        
        <el-table
            :data="locomotives"
            ref="locomotivesTable"
            fit
            highlight-current-row
            border
            style="width: 100%;"
            align="center"
        >
            <el-table-column :min-width="8"     label="ID"                  prop="id"       align="center"></el-table-column>
            <el-table-column :min-width="25"    label="Наименование"        prop="title"    align="center"></el-table-column>
            <el-table-column :min-width="8"     label="Тип"                                 align="center">
                <template slot-scope="scope">
                    {{ locomotiveGroups.length == 0 ? 
                            "" : 
                            locomotiveGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Серийный №"     prop="serial_number"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_locomotive_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-locomotive'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditLocomotive(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeLocomotive(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="locomotivePagination.total>0" :total="locomotivePagination.total" :page.sync="locomotivePagination.page" :limit.sync="locomotivePagination.per_page" @pagination="loadLocomotives" />
        <AddLocomotive v-if="checkPermission(['manage catalog-locomotive'])" :show="showAddLocomotive" :close="handleCloseAddLocomotive"></AddLocomotive>
        <EditLocomotive v-if="editingLocomotiveId && checkPermission(['manage catalog-locomotive'])" :locomotiveId="editingLocomotiveId" :show="showEditLocomotive" :close="handleCloseEditLocomotive"></EditLocomotive>
    </div>
</template>


<script>
import {getAllLocomotives, getLocomotiveGroups} from "@/api/locomotives";
import Pagination from '@/components/Pagination';
import { deleteLocomotive } from "@/api/locomotives";
import AddLocomotive from "./components/AddLocomotive.vue";
import EditLocomotive from "./components/EditLocomotive.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddLocomotive, EditLocomotive },
    async created() {
        await this.loadTypes();
        await this.loadLocomotives();
    },
    data() {
        return {
            showAddLocomotive: false,

            showEditLocomotive: false, 
            editingLocomotiveId: null,

            locomotiveGroups: [],

            locomotivePagination: {
                place: ``,
                search: ``,
                serial_number: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            locomotives: [
                {
                    id: 1, 
                    id_group: 1, 
                    title: "ЭКГ - 1", 
                    status: 1, 
                }
            ], 
            
        };
    },
    methods: {
        checkPermission,
        async loadLocomotives() {
            const {pagination} = await getAllLocomotives(this.locomotivePagination);
            this.locomotivePagination = {
                place: this.locomotivePagination.place,
                search: this.locomotivePagination.search,
                serial_number: this.locomotivePagination.serial_number,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.locomotives = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getLocomotiveGroups();
            this.locomotiveGroups = data;
        },
        handleShowAddLocomotive()
        {
            this.showAddLocomotive = true;
        }, 
        handleCloseAddLocomotive() {
            this.showAddLocomotive = false;
            this.loadLocomotives();
        },

        handleShowEditLocomotive(locomotiveId) 
        {
            this.editingLocomotiveId = locomotiveId; 
            this.showEditLocomotive = true;
        }, 
        handleCloseEditLocomotive() 
        {
            this.editingLocomotiveId = null;
            this.showEditLocomotive = false;
            this.loadLocomotives();
        }, 

        async removeLocomotive(locomotiveId) {
            try { 
                const conf = await this.$confirm(`При удалении Тепловоза все данные могут быть утеряны! Вы точно хотите удалить Тепловоз?`,
                                                    `Внимание`, 
                                                    {
                                                        type:"danger",
                                                    });
                if(conf != "confirm")
                    return;

            }
            catch(e)
            {
                console.log(e);
                return;
            }
            const res = await deleteLocomotive(locomotiveId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"Тепловоз удалён!", 
                    duration: 5*1000,
                });
                this.loadLocomotives();
                return;
            }
            this.$message({
                type:"error",
                message:res.message, 
                duration: 5*1000,
            });
            return;
        }
    }
}

</script>