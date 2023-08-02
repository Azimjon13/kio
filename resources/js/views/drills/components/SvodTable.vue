<template>
    <div class="app-container">
        <el-row :gutter="20">
            <el-col :span="12">
                <div style="text-align: center; font-size: 16px; font-weight: bold;">КИО</div>
                <el-table
                    :data="kio"
                    fit  highlight-current-row border style="width: 100%;" align="center" 
                >
                    <el-table-column label="Сутки" align="center" prop="day" label-class-name="svod-table-bg-color">
                    </el-table-column>
                    <el-table-column label="С начала месяца" align="center" prop="since" label-class-name="svod-table-bg-color">
                    </el-table-column>
                </el-table>
                <br>
                <div style="text-align: center; font-size: 16px; font-weight: bold;">Бурение (п.м.)</div>
                <el-table
                    :data="plansFacts"
                    fit  highlight-current-row border style="width: 100%;" align="center" 
                >
                    <el-table-column label="Сутки" align="center">
                        <el-table-column label="План" prop="plan" align="center"></el-table-column>
                        <el-table-column label="Факт" prop="fact" align="center"></el-table-column>
                        <el-table-column label="%" prop="percentDay" align="center"></el-table-column>
                    </el-table-column>
                    <el-table-column label="С начала месяца" align="center">
                        <el-table-column label="План" prop="plan_month" align="center"></el-table-column>
                        <el-table-column label="Факт" prop="fact_month" align="center"></el-table-column>
                        <el-table-column label="%" prop="percentMonth" align="center"></el-table-column>
                    </el-table-column>
                </el-table>
               
            </el-col>
       
            <el-col :span="12">
                <div style="text-align: center; font-size: 16px; font-weight: bold;">Простои</div>
                <el-table
                    :data="downtimesByReasons"
                    fit  highlight-current-row border style="width: 100%;" align="center" 
                    :row-style="{'height': '52px'}"
                    class="firstRowBold"
                >
                    <el-table-column label="" prop="title" label-class-name="svod-table-bg-color">
                        <template slot-scope="scope">
                            <span v-if="scope.row.keyType" class="openByReasonTypeDialogClass" @click="openByReasonTypeDialog(scope.row)">{{ scope.row.title }}</span>
                            <span v-else> {{ scope.row.title }}</span>
                        </template>
                    </el-table-column>
                    <el-table-column label="За сутки" prop="day" align="center" label-class-name="svod-table-bg-color"></el-table-column>
                    <el-table-column label="С начала месяца" prop="since" align="center" label-class-name="svod-table-bg-color"></el-table-column>
                </el-table>
                <br>
            </el-col>
        </el-row>
        <el-dialog :title="'Простои по типу: ' + byReasonType.title + '. По площадке: ' + placeInfo.place + ` (${placeInfo.manager}) ` +' на дату: ' + (new Date(this.date).toLocaleDateString())" :visible.show="showDialogByReasonType" :before-close="closeByReasonTypeDialog">
            <el-table 
                :data="byReasonType.downtimes"
                fit  highlight-current-row border style="width: 100%;" align="center" 
                class="lastRowBold"
            >
                <el-table-column label="Наименование вида простоя" prop="title" label-class-name="svod-table-bg-color">
                    <template slot-scope="scope">
                        <span v-if="scope.row.itSummary" style="font-weight: bold;">Всего {{ scope.row.title.toLowerCase() }}</span>
                        <span v-else class="byReasonClass" @click="openDowntimesByReason(scope.row.id_reason)">{{ scope.row.title }}</span>
                    </template>
                </el-table-column>
                <el-table-column label="За сутки" prop="day" align="center" label-class-name="svod-table-bg-color"></el-table-column>
                <el-table-column label="С начала месяца" prop="since" align="center" label-class-name="svod-table-bg-color"></el-table-column>
            </el-table>
        </el-dialog>
        <DowntimesByReason v-if="selectedDowntimesByReasonId" :reasonDowntimesItog="reasonDowntimesItog" :show="showDowntimesByReason" :close="closeDowntimesByReason" :id_reason="selectedDowntimesByReasonId" :id_place="id_place" :date="date"></DowntimesByReason>

    </div>
</template>


<script>
import {getPlaceForExcel} from "@/api/places";
import DowntimesByReason from './DowntimesByReason.vue';
export default { 
    components: { DowntimesByReason, },
    props: {
        itogData: Object,
        date: String, 
        id_place: Number,
       
    }, 
    watch: {
        itogData: function(newVal, oldVal)
        {
            this.recalcAfterUpdate(newVal);
        }
    },
    created() {
        this.loadPlaceInfo();
    },
    data() {
        return { 
            showDialogByReasonType: false, 

            showDowntimesByReason: false, 
            selectedDowntimesByReasonId: null,
            reasonDowntimesItog: {
                day: 0,
                since: 0,
            },



            kio: [{
                day: this.itogData.kio.day,
                since: this.itogData.kio.since,
            }],
            plansFacts: [{
                plan: this.itogData.plan.toLocaleString(), 
                fact: this.itogData.fact.toLocaleString(), 
                percentDay: this.itogData.percentDay, 
                plan_month: this.itogData.plan_month.toLocaleString(),
                fact_month: this.itogData.fact_month.toLocaleString(), 
                percentMonth: this.itogData.percentMonth,
            }], 

            downtimesByReasons: [
                {
                    title:"Всего", 
                    day: this.itogData.downtimes.summary.dayDowntime,
                    since: this.itogData.downtimes.summary.sinceDowntime,
                },
                {
                    title:"Плановые", 
                    day: this.itogData.downtimes.planned.dayDowntime,
                    since: this.itogData.downtimes.planned.sinceDowntime,
                    keyType: 1, 
                },
                {
                    title:"Внеплановые", 
                    day: this.itogData.downtimes.unscheduled.dayDowntime,
                    since: this.itogData.downtimes.unscheduled.sinceDowntime,
                    keyType: 2,
                },
            ], 

            placeName: "",
            byReasonType: {
                title: "", 
                downtimes: [],
            },
            update: 0,
            placeInfo: {
                place: "",
                parent: "",
                manager: "",
            }
        } 
    },
    methods: {
        async loadPlaceInfo() 
        { 
            const {data} = await getPlaceForExcel(this.id_place);
            this.placeInfo = { 
                place: data.place_title, 
                parent: data.parent_title, 
                manager: data.manager_title,
            };

        },
        recalcAfterUpdate(newItogData) 
        {
            this.kio = [{
                day: newItogData.kio.day,
                since: newItogData.kio.since,
            }],
            this.plansFacts = [{
                plan: newItogData.plan.toLocaleString(), 
                fact: newItogData.fact.toLocaleString(), 
                percentDay: newItogData.percentDay, 
                plan_month: newItogData.plan_month.toLocaleString(),
                fact_month: newItogData.fact_month.toLocaleString(), 
                percentMonth: newItogData.percentMonth,
            }], 

            this.downtimesByReasons = [
                {
                    title:"Всего", 
                    day: newItogData.downtimes.summary.dayDowntime,
                    since: newItogData.downtimes.summary.sinceDowntime,
                },
                {
                    title:"Плановые", 
                    day: newItogData.downtimes.planned.dayDowntime,
                    since: newItogData.downtimes.planned.sinceDowntime,
                    keyType: 1, 
                },
                {
                    title:"Внеплановые", 
                    day: newItogData.downtimes.unscheduled.dayDowntime,
                    since: newItogData.downtimes.unscheduled.sinceDowntime,
                    keyType: 2,
                },
            ];
        },
        openByReasonTypeDialog(reasonType) 
        {
            const downtimes = this.itogData.downtimes;
            let downtimesByReasonType = [];
            Object.keys(downtimes).forEach(key => {
                if(downtimes[key].type == reasonType.keyType) 
                {
                    downtimesByReasonType.push({
                        id_reason: key,
                        title: downtimes[key].reason,
                        day: +downtimes[key].dayDowntime,
                        since: +downtimes[key].sinceDowntime,
                    });
                }
            });
            
            downtimesByReasonType.sort((a,b) => (a.day - b.day));
            downtimesByReasonType = downtimesByReasonType.reverse();

            downtimesByReasonType.push({
                title: reasonType.title,
                day: reasonType.day, 
                since: reasonType.since,
                itSummary: true,
            });
            this.byReasonType.title = reasonType.title;
            this.byReasonType.downtimes = downtimesByReasonType;
            this.showDialogByReasonType = true;
        },
        closeByReasonTypeDialog()
        {
            this.showDialogByReasonType = false;
        }, 

        openDowntimesByReason(id_reason) 
        {
            const day = this.itogData.downtimes[`${id_reason}`].dayDowntime; 
            const since = this.itogData.downtimes[`${id_reason}`].sinceDowntime;
            this.reasonDowntimesItog = {
                day, since
            };
            console.log(this.reasonDowntimesItog);
            this.selectedDowntimesByReasonId = id_reason;
            this.showDowntimesByReason = true;
        }, 
        closeDowntimesByReason()
        {
            this.showDowntimesByReason = false;
            this.selectedDowntimesByReasonId = null; 
        },
    }

}

</script>

<style >
/* #F5F7FA */

th.svod-table-bg-color { 
    background-color: #F5F7FA !important; 
}
.openByReasonTypeDialogClass:hover
{
    cursor: pointer;
    text-decoration: underline;

}
.openByReasonTypeDialogClass, .byReasonClass { 
    color: blue;
}
.byReasonClass:hover { 
    cursor: pointer;
    text-decoration: underline;
}
.firstRowBold table.el-table__body .el-table__row:first-child, 
.lastRowBold table.el-table__body .el-table__row:last-child { 
    font-weight: bold !important;
}
</style>