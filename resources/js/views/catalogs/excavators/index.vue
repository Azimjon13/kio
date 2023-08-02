<template>
    <div class="app-container">
        
        <h4>
            Справочник Экскаваторов
            <br>
            <el-input v-model="excavatorPagination.search" placeholder="Введите наименование" style="width: 300px;"></el-input>
            <el-input v-model="excavatorPagination.garage" placeholder="Введите Гаражный №" style="width: 250px;"></el-input>
            <el-input v-model="excavatorPagination.place"  placeholder="Введите Местоположение" style="width: 300px;"></el-input>
            <el-button type="success" @click="loadExcavators">Поиск</el-button>
            <el-button v-if="checkPermission(['manage catalog-excavator'])" style="float:right;" type="success" icon="el-icon-plus" @click="handleShowAddExcavator">Добавить экскаватор</el-button>
        </h4>
        
        <el-table
            :data="excavators"
            ref="excavatorsTable"
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
                    {{ excavatorGroups.length == 0 ? 
                            "" : 
                            excavatorGroups.find(g=> g.id == scope.row.id_group).name 
                    }}
                </template>
            </el-table-column>
            <el-table-column :min-width="12"    label="Тек. гар. номер"     prop="latest_excavator_place.garage"        align="center"></el-table-column>
            <el-table-column :min-width="20"    label="Тек. местоположение" prop="latest_excavator_place.place.title"   align="center"></el-table-column>
            <el-table-column :min-width="12"    label="Статус" align="center">
                <template slot-scope="scope">
                    {{ scope.row.status == 1 ? "Работает" : "Списан" }}
                </template>
            </el-table-column>
            <el-table-column :min-width="15"    label="Действия" align="center">
                <template slot-scope="scope">
                    <div  v-if="checkPermission(['manage catalog-excavator'])" style="text-align: center;" >
                        <el-button type="info" icon="el-icon-edit" circle @click="handleShowEditExcavator(scope.row.id)"></el-button>
                        <el-button type="danger" icon="el-icon-delete" circle @click="removeExcavator(scope.row.id)"></el-button>
                    </div>
                    <div v-else> недосточно прав </div>
                </template>
            </el-table-column>
        </el-table>
        <Pagination v-show="excavatorPagination.total>0" :total="excavatorPagination.total" :page.sync="excavatorPagination.page" :limit.sync="excavatorPagination.per_page" @pagination="loadExcavators" />
        <AddExcavator v-if="checkPermission(['manage catalog-excavator'])" :show="showAddExcavator" :close="handleCloseAddExcavator"></AddExcavator>
        <EditExcavator v-if="editingExcavatorId && checkPermission(['manage catalog-excavator'])" :excavatorId="editingExcavatorId" :show="showEditExcavator" :close="handleCloseEditExcavator"></EditExcavator>
    </div>
</template>


<script>
import {getAllExcavators, getExcavatorGroups} from "@/api/excavators";
import Pagination from '@/components/Pagination';
import { deleteExcavator } from "../../../api/excavators";
import AddExcavator from "./components/AddExcavator.vue";
import EditExcavator from "./components/EditExcavator.vue";
import checkPermission from '@/utils/permission'; // Permission checking

export default {
    components: { Pagination, AddExcavator, EditExcavator },
    async created() {
        await this.loadTypes();
        await this.loadExcavators();
    },
    data() {
        return {
            showAddExcavator: false,

            showEditExcavator: false, 
            editingExcavatorId: null,

            excavatorGroups: [],

            excavatorPagination: {
                place: ``,
                search: '',
                garage: ``,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            excavators: [
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
        async loadExcavators() {
            const {pagination} = await getAllExcavators(this.excavatorPagination);
            this.excavatorPagination = {
                place: this.excavatorPagination.place,
                search: this.excavatorPagination.search,
                garage: this.excavatorPagination.garage,
                page: pagination.current_page, 
                per_page: pagination.per_page,
                total: pagination.total, 
                sort: `+id`,
            };
            this.excavators = pagination.data;
          
        },
        async loadTypes() {
            const {data} = await getExcavatorGroups();
            this.excavatorGroups = data;
        },
        handleShowAddExcavator()
        {
            this.showAddExcavator = true;
        }, 
        handleCloseAddExcavator() {
            this.showAddExcavator = false;
            this.loadExcavators();
        },

        handleShowEditExcavator(excavatorId) 
        {
            this.editingExcavatorId = excavatorId; 
            this.showEditExcavator = true;
        }, 
        handleCloseEditExcavator() 
        {
            this.editingExcavatorId = null;
            this.showEditExcavator = false;
            this.loadExcavators();
        }, 

        async removeExcavator(excavatorId) {
            try { 
                const conf = await this.$confirm(`При удалении экскаватора все данные могут быть утеряны! Вы точно хотите удалить экскаватор?`,
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
            const res = await deleteExcavator(excavatorId);
            if(res.status && !res.errors)
            {
                this.$message({
                    type:"success",
                    message:"Экскаватор удалён!", 
                    duration: 5*1000,
                });
                this.loadExcavators();
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