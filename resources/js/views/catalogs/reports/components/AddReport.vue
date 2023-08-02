<template>
    <el-dialog title="Добавление Отчёта" width="60%" :visible.sync="this.show" :before-close="this.close">
        <div class="form-container">
            <el-form :model="report" label-position="top">
                <el-row :gutter="20">
                    <el-col :span="12">
                        <el-form-item label="Описание">
                            <el-input v-model="report.description"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="Тип техники" >
                            <el-select v-model="report.id_technica_type" @change="afterSelectManagerOrTechnicaType" style="width:100%;">
                                <el-option v-for="tp in technica_types" :label="tp.title" :value="tp.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-row :gutter="20">
                    <el-col :span="8">
                        <el-form-item label="Подразделение">
                            <el-select v-model="report.id_manager" @change="afterSelectManagerOrTechnicaType" style="width: 100%;">
                                <el-option v-for="manager in managers" :label="manager.title" :value="manager.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Рудник" >
                            <el-select v-model="report.id_parent" @change="afterSelectRudnik" style="width: 100%;">
                                <el-option v-for="place in rudniks" :label="place.title" :value="place.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="Площадка" >
                            <el-select v-model="report.id_place" style="width: 100%;">
                                <el-option v-for="place in placesList" :label="place.title" :value="place.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                <hr>


                <h3>Коды простоев</h3>
                <el-collapse v-model="collapseActiveReasonsNames">
                    <el-collapse-item v-for="reason in reasons" :title="reason.reason" :name="reason.id">
                        <template slot="title">
                            <div class="reasonTitleDiv" style="width: 80%;">
                                <span> {{ reason.reason }}</span>
                                <span style="float: right;"> <b>Тип:</b> {{ reason.type == 1 ? "Плановый" : (reason.type == 2 ? "Внеплановый" : "Эксп. вне объёма") }}</span>
                            </div>
                        </template>
                        <el-table 
                            :data="reason.codes"
                            fit  highlight-current-row border style="width: 100%;" align="center"
                        >
                            <el-table-column label="Код" prop="code" :min-width="8" align="center"></el-table-column>
                            <el-table-column label="Описание кода" prop="title" :min-width="55"></el-table-column>
                            <el-table-column label="Тип техники" prop="title_technica_group" :min-width="17" align="center"></el-table-column>
                            <el-table-column label="Включён в отчёт" :min-width="20" align="center">
                                <template slot-scope="scope">
                                    <el-switch
                                        v-model="scope.row.selected"
                                        active-text="Да"
                                        inactive-text="Нет"
                                        :active-value="true"
                                        :inactive-value="false"
                                    >
                                    </el-switch>
                                </template>
                            </el-table-column>
                        </el-table>
                    </el-collapse-item>
                </el-collapse>
            </el-form>
            <span slot="footer" class="dialog-footer">
                <el-button type="primary" @click="save">
                    Сохранить
                </el-button>
                <el-button type="danger" @click="close">
                    {{ $t('table.cancel') }}
                </el-button>
            </span>
        </div>
    </el-dialog>
</template>

<script>
import {technicaTypes} from "@/api/technica";
import {getManager, fetchPlaces} from "@/api/places";
import {reasonsWithCodes} from "@/api/reasons";
import { storeReport } from "../../../../api/reports";


export default  {
    props: {
        show: Boolean,
        close: Function
    },
    created() {
        this.loadManagers();
        this.loadTechnicaTypes();
    },
    data() {
        return { 
            collapseActiveReasonsNames: [], 

            technica_types: [], 
            managers: [],
            placesList: [], 
            rudniks: [], 
            reasons: [],
           
            
            report: { 
                id_manager: null, 
                id_parent: null, 
                id_technica_type: null, 
                id_place: null, 
                description: "",
            }
        }
    }, 
    methods: {
        async loadManagers() 
        {
            const {data} = await fetchPlaces();
            this.managers = data.map(r => ({id: r.id, title: r.title}));
        },
        async loadTechnicaTypes() {
            const {data} = await technicaTypes();
            this.technica_types = data;
        }, 
        async afterSelectManagerOrTechnicaType()
        {
            if(this.report.id_technica_type && this.report.id_manager)
            {
                this.report.id_place = null; 
                this.report.id_parent = null; 

                const aggregate = '/'+this.technica_types.find(t => t.id == this.report.id_technica_type).aggregate;

                const res = await reasonsWithCodes(aggregate);
                this.reasons = res.data.map(r => ({
                    ...r, 
                    codes: r.codes.map(c => ({...c, selected: false})),
                }));

                const {data} = await getManager(this.report.id_manager, aggregate);
                this.rudniks = data.places;
            }
        }, 
        async afterSelectRudnik() 
        {
            this.report.id_place = null;
            this.placesList =  this.rudniks.find(f => f.id == this.report.id_parent).places;
        }, 
        async save() 
        {
            const codesList = this.reasons.flatMap(r=>r.codes).filter(r=>r.selected).map(r=>r.id);
            if(codesList.length === 0 )
            {
                this.$message({
                    type:"error", 
                    message:"Вы не выбрали ни одного кода!",
                    duration: 5*1000
                });
                return;
            }
            if(!this.report.id_place) {
                this.$message({
                    type:'error',
                    message:"Вы не выбрали площадку для отчёта",
                    duration: 5*1000
                });
                return;
            }
            if(!this.report.description.trim())
            {
                this.$message({
                    type:'error',
                    message:"Вы не ввели описание отчёта",
                    duration:5*1000
                });
                return;
            }

            const request = {
                description: this.report.description, 
                id_place: this.report.id_place, 
                id_technica_type: this.report.id_technica_type, 
                codes: codesList
            }; 

            const res = await storeReport(request);
            if(res.status && !res.errors) 
            {
                this.$message({
                    message:`Отчёт успешно создан!`, 
                    type:"success",
                    duration:5*1000
                });
                this.report = {
                    id_manager: null, 
                    id_parent: null, 
                    id_technica_type: null, 
                    id_place: null, 
                    description: "",
                };
                this.reasons = [];
                this.close();
                return;
            }
        
            console.log(res.errors);
            this.$message({
                message: res.message, 
                type:'error',
                duration: 5*1000
            });
            return;
            
        }
    }

}
</script>