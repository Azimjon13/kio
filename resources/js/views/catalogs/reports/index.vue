<template>
    <div class="app-container">
        <h3>Справочник отчётов</h3>


        <el-input placeholder="Введите для поиска" v-model="reportsPagination.search" style="width: 300px;"></el-input>
        <el-select placeholder="Выберите тип техники" v-model="reportsPagination.id_technica_type" clearable>
            <el-option v-for="tt in technicaTypes" :label="tt.title" :value="tt.id"></el-option>
        </el-select>
        <el-button type="success" @click="loadReports">Поиск</el-button>
        <el-button type="success" icon="el-icon-plus" style="float:right" @click="openAddReport" v-if="checkPermission(['manage catalog-reports'])"> Добавить отчёт </el-button>

        <hr/>

        <el-table
                :data="reportsList"
                fit  highlight-current-row border style="width: 100%;" align="center"
                ref="reportsList"
        >
            <el-table-column label="ID" prop="id" :min-width="6" align="center"></el-table-column>
            <el-table-column label="Площадка" prop="place" :min-width="25" align="center"></el-table-column>
            <el-table-column label="Тип техники" prop="technica_type" :min-width="25" align="center"></el-table-column>
            <el-table-column label="Описание отчёта" prop="description" :min-width="25" align="center"></el-table-column>
            <el-table-column label="Действия" :min-width="19" align="center">
                <template slot-scope="scope" align="center">
                    <el-button  type="info" icon="el-icon-edit" circle @click="openEditReport(scope.row.id)"></el-button>
                    <el-button  type="danger" icon="el-icon-delete" circle @click="removeReport(scope.row.id)" v-if="checkPermission(['manage catalog-reports'])"></el-button>
                </template>
            </el-table-column>
        </el-table>
        <AddReport :show="showAddReportDialog" :close="closeAddReport"></AddReport>
        <EditReport v-if="showEditReportId" :id_report="showEditReportId" :show="showEditReportDialog" :close="closeEditReport"></EditReport>
        <Pagination v-show="reportsPagination.total>0" :total="reportsPagination.total" :page.sync="reportsPagination.page" :limit.sync="reportsPagination.per_page" @pagination="loadReports" />
        
    </div>
</template>

<script>
import AddReport from './components/AddReport.vue';
import EditReport from './components/EditReport.vue';
import { getReports, deleteReport } from '../../../api/reports';
import { technicaTypes } from "../../../api/technica";
import Pagination from '@/components/Pagination';
import checkPermission from '@/utils/permission'; // Permission checking

 
export default { 
    created() {
        this.loadTechnicaTypes();
        this.loadReports();
    },
    components: { AddReport, Pagination, EditReport }, 
    data() {
        return {
            
            showAddReportDialog: null,
            
            showEditReportDialog: null, 
            showEditReportId: null, 

            technicaTypes: [],
            reportsPagination: {
                search: "",
                id_place: null,
                id_technica_type: null,
                page: 1,
                per_page: 10, 
                total:0,
                sort: `+id`,
            },

            reportsList: [],
        }
    }, 
    methods: {
        checkPermission,
        async loadTechnicaTypes() {
            const {data} = await technicaTypes();
            this.technicaTypes = data;
        },
        async loadReports() 
        {
            const {pagination} = await getReports(this.reportsPagination);
            this.reportsList = pagination.data;
            this.reportsPagination = {
                per_page: pagination.per_page,
                total: pagination.total,
                page: pagination.current_page,

                search: this.reportsPagination.search,
                id_place: this.reportsPagination.id_place,
                id_technica_type: this.reportsPagination.id_technica_type,
                sort: this.reportsPagination.sort,
            };
        },
        openAddReport()
        {
            this.showAddReportDialog = true;
        }, 
        closeAddReport() 
        {
            this.showAddReportDialog = false;
            this.loadReports();
        },

        openEditReport(id_report) 
        {
            this.showEditReportId = id_report;
            this.showEditReportDialog = true;
        }, 
        closeEditReport() 
        {
            this.showAddReportDialog = false; 
            this.showEditReportId = null;
            this.loadReports();
        },

        async removeReport(reportId)
        {
            try {
                let conf = await this.$confirm(`Вы точно хотите удалить этот отчёт?`, 'Внимание!', {type:"warning"});
                if(conf != "confirm")
                    return;
            }
            catch(e)
            {
                console.log(e);
                return;
            }
            const res = await deleteReport(reportId);
            
            if(res.status && !res.errors) 
            {
                this.$message({
                    message:"Отчёт успешно был удалён!",
                    type: "success",
                    duration: 5*1000
                });
                this.loadReports();
                return;
            }


        },


    }

}
</script>