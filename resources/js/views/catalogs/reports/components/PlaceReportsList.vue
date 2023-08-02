<template>
    <el-dialog title="Список отчётов по площадке" width="60%" :visible.sync="this.show" :before-close="this.close">
        <div class="form-container">
            <el-table
                :data="reportsList"
                fit  highlight-current-row border style="width: 100%;" align="center"
            >
                <el-table-column label="№ п/п" :min-width="10">
                    <template slot-scope="scope">{{ scope.$index+1 }}</template>
                </el-table-column>
                <el-table-column label="Описание" :min-width="70" header-align="center" prop="description"></el-table-column>
                <el-table-column label="Действия" :min-width="20">
                    <template slot-scope="scope">
                        <router-link :to="'/excavators/'+routerLink+'/'+scope.row.id">
                            <el-button type="success">Открыть отчёт</el-button>
                        </router-link>
                    </template>
                </el-table-column>
            </el-table>

        </div>
    </el-dialog>
</template>


<script>
import { getReports } from '../../../../api/reports';

export default { 
    props: {
        id_technica_type: Number, 
        id_place: Number,
        date: String,
        routerLink: String,
        
        show: Boolean,
        close: Function,
    }, 
    created() {
        this.loadReports();
    },
    data() {
        return { 
            reportsList: []
        }
    },
    methods: {
        async loadReports() 
        {
            const res = await getReports({id_technica_type: this.id_technica_type, id_place: this.id_place, page: 1, per_page: 200});
            this.reportsList = res.pagination.data;
            
        },
    },

}
</script>