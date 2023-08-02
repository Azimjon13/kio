<template>
     <el-dialog title="Добавление Вида простоя" width="40%" :visible.sync="this.show" :before-close="this.close">
            <div class="form-container">
                <el-form :model="reason">
                    <el-descriptions :column="1" border>
                        <el-descriptions-item >
                            <template slot="label">
                                Тип простоя
                            </template>
                            <el-select v-model="reason.type">
                                <el-option :value="1" label="Плановый"></el-option>
                                <el-option :value="2" label="Внеплановый"></el-option>
                                <el-option :value="3" label="Эксп. вне объёма"></el-option>
                            </el-select>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Наименование
                            </template>
                            <el-input v-model="reason.reason"></el-input>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Экскаваторы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.excavator"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                CБШ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.drill"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ЦПТ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.cpt"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Автосамосвалы
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.truck"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                ДСМ
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.dsm"></el-switch>
                        </el-descriptions-item>
                        <el-descriptions-item >
                            <template slot="label">
                                Локомотив
                            </template>
                            <el-switch active-text="Да" inactive-text="Нет" :active-value="1" :inactive-value="0" v-model="reason.locomotive"></el-switch>
                        </el-descriptions-item>
                    </el-descriptions>
                </el-form>
                <br>
                <span slot="footer" class="dialog-footer" style="float:right;">
                    <el-button type="primary" @click="saveReason">
                        Добавить
                    </el-button>
                    <el-button type="danger" @click="this.close()">
                        {{ $t('table.cancel') }}
                    </el-button>
                </span>
                <br><br>
            </div>
        </el-dialog>
</template>

<script>
import { storeReason } from '@/api/reasons';

export default {
    props: {
        show: Boolean,
        close: Function
    },
    data() {
        return {
            reason: {
                reason:"",
                type: 1, 
                excavator: 0,
                drill: 0, 
                cpt: 0, 
                truck: 0, 
                dsm: 0, 
                locomotive: 0,
            },
        }
    }, 
    methods: {
        async saveReason()
        {
            const res = await storeReason(this.reason);

            if(res.status && res.errors == null)
            {
                this.$message({
                    message: 'Причина успешно добавлена!',
                    type: 'success',
                    duration: 5 * 1000,
                });
                this.reason = {
                    reason:"",
                    type: 1, 
                    excavator: 0,
                    drill: 0, 
                    cpt: 0, 
                    truck: 0, 
                    dsm: 0, 
                    locomotive: 0,
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