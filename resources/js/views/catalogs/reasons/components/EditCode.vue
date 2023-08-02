<template>
    <el-dialog title="Редактирование кода простоя" width="40%" :visible.sync="this.show" :before-close="this.close">
           <div class="form-container">
               <el-form :model="code">
                   <el-descriptions :column="1" border>
                        <el-descriptions-item >
                           <template slot="label">
                               Тип простоя
                           </template>
                           <el-select v-model="code.type" @change="refreshReasons">
                               <el-option :value="1" label="Плановый"></el-option>
                               <el-option :value="2" label="Внеплановый"></el-option>
                               <el-option :value="3" label="Эксп. вне объёма"></el-option>
                           </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                           <template slot="label">
                               Выберите вид техники
                           </template>
                           <el-select v-model="code.selectedTechnicaType" value-key="id" @change="handleSelectTechnicaType">
                               <el-option v-for="tp in technica_types"  :value="tp" :label="tp.title"></el-option>
                           </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                           <template slot="label">
                               Выберите тип техники
                           </template>
                           <el-select v-model="code.id_technica_group">
                               <el-option v-for="g in groups"  :value="g.id" :label="g.name"></el-option>
                           </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                           <template slot="label">
                               Выберите простой
                           </template>
                           <el-select v-model="code.id_reason">
                               <el-option v-for="r in reasonsList"  :value="r.id" :label="r.reason"></el-option>
                           </el-select>
                        </el-descriptions-item>
                       <el-descriptions-item >
                           <template slot="label">
                               Код простоя
                           </template>
                           <el-input v-model="code.code"></el-input>
                       </el-descriptions-item>
                       
                       <el-descriptions-item >
                           <template slot="label">
                               Описание
                           </template>
                           <el-input type="textarea" v-model="code.title"></el-input>
                       </el-descriptions-item>
                   </el-descriptions>
               </el-form>
               <br>
               <span slot="footer" class="dialog-footer" style="float:right;">
                   <el-button type="primary" @click="saveCode">
                       Добавить
                   </el-button>
                   <el-button type="danger" @click="this.close">
                       {{ $t('table.cancel') }}
                   </el-button>
               </span>
               <br><br>
           </div>
       </el-dialog>
</template>

<script>
import { updateCode, getCode } from '@/api/codes';
import { technicaGroups, technicaTypes } from '@/api/technica';
import { getReasonsSelect, getReason } from "@/api/reasons";

export default {
   props: {
        show: Boolean,
        close: Function,
        codeId: Number,
   },
   async created() {
        await this.loadTechnicaGroups();
        await this.loadTechnicaTypes();
        this.loadCode();
   },
   data() {
        return {

            technica_types: [],
            technica_groups: [], 
            groups: [],
            reasonsList: [],
            code: {
                selectedTechnicaType: null,
                type: null, 
                id_reason: null,
                id_technica_group: null,
                code: '',
                title: "", 
            },
        }
   }, 
   methods: {
        async loadCode() {
            const {data} = await getCode(this.codeId);
            const reason = await getReason(data.id_reason);
            const idTechnicaType = this.technica_groups.find(a => a.id == data.id_technica_group).id_technica_type;

            const techType = this.technica_types.find(t => t.id == idTechnicaType);
            this.groups = this.technica_groups.filter(i => i.id_technica_type == idTechnicaType);

            this.code = {
                ...data, 
                type: reason.data.type,
                selectedTechnicaType: techType
            };
            await this.refreshReasons();
            this.code.id_reason = data.id_reason;

        },
        async loadTechnicaTypes() 
        {
            const {data} = await technicaTypes();
            this.technica_types = data;
            console.log(data);
        },
        async loadTechnicaGroups() {
            const {data} = await technicaGroups();
            this.technica_groups = data; 
            console.log(data);
        },

        async handleSelectTechnicaType(selectedType) 
        {
            console.log(selectedType);
            this.groups = this.technica_groups.filter(g => g.id_technica_type == selectedType.id);
            this.code.id_technica_group = null;
            this.refreshReasons();
        },
        async refreshReasons() {
            if(this.code.type && this.code.selectedTechnicaType) 
            {
                const params = {
                    type: this.code.type,
                    technica_type: this.code.selectedTechnicaType.aggregate,
                };
                const {data} = await getReasonsSelect(params);
                this.reasonsList = data;
                this.code.id_reason = null;
            }
        },
        async saveCode()
        {
            const res = await updateCode(this.codeId, this.code);

            if(res.status && res.errors == null)
            {
                this.$message({
                    message: 'Код простоя успешно добавлена!',
                    type: 'success',
                    duration: 5 * 1000,
                });
                this.code = {
                    type: null, 
                    id_reason: null,
                    id_technica_group: null,
                    code: '',
                    title: "", 
                };
                
                this.close();
                return;
            } 
            this.$message({
                message: res.message,
                type: 'error',
                duration: 5 * 1000,
            });
        }
   },
}


</script>