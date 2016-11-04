<template>
    <form class="form">
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.name" id="content_name">
            <label for="content_name">Nom *</label>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.block" id="content_block">
            <label for="content_block">Bloc *</label>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" v-model="content.module.name" readonly id="content_module">
            <label for="content_module">Module</label>
        </div>
        <div>
            <h5>Configuration :</h5>
            <table class="table table-bordered no-margin">
                <tbody>
                <tr v-for="(db,i) in content.data.db">
                    <td>{{i}}</td>
                    <td>
                        <div class="form-group">
                            <select :id="'db_table_'+i" v-model="db.alias" class="form-control">
                                <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                            </select>
                            <label :for="'db_table_'+i">Table</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select :id="'db_column_'+i" v-model="db.column" class="form-control">
                                <option v-for="column in columns" :value="column">{{column}}</option>
                            </select>
                            <label :for="'db_column_'+i">Colonne</label>
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <select :id="'db_value_'+i" class="stylesheets-select form-control select2-list" multiple>
                                <option v-for="value in getValues(db.alias)" :value="value.id">{{value.title}}</option>
                            </select>
                            <label :for="'db_value_'+i">Valeur</label>
                        </div>
                        <div class="form-group">
                            <input type="text" v-model="db.route" class="form-control" :id="'db_route_'+i">
                            <label :for="'db_route_'+i">Route</label>
                        </div>
                    </td>
                    <td>
                        <button type="button" @click="removeDbField(i)" class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <button type="button" @click="addDbField" class="btn ink-reaction pull-right btn-floating-action btn-info add-route"><i class="fa fa-plus"></i></button>
        </div>
    </form>
</template>

<script type="text/babel">

    /* CSS */
    import '../../../../../Blocks/AdminBlock/Resources/public/css/libs/select2/select2.css'

    /* JS*/
    import '../../../../../Blocks/AdminBlock/Resources/public/js/libs/select2/select2.min'
    import {AppVendor} from '../../../../../Blocks/AdminBlock/Resources/public/js/app'

    export default{
        name: 'list-post',
        props: {
            content: {
                type: Object,
                required: true
            },
            page: {
                required: true
            },
            website: {
                required: true
            }
        },
        data(){
            return {
                tables : {
                    p: 'Article',
                    c: 'Catégorie'
                },
                columns: ['id','slug'],
                selectValues: null
            }
        },
        computed: {

        },
        methods: {
            addDbField(){
                this.content.data.db.push({
                    alias: '',
                    column: '',
                    value: [],
                    route: ''
                });
            },
            removeDbField(index){
                this.content.data.db.splice(index, 1);
            },
            addLinkField(){
                this.content.data.link.push({
                    alias: '',
                    column: '',
                    route: '',
                });
            },
            removeLinkField(index){
                this.content.data.link.splice(index, 1);
            },
            getValues(table){

            }
        },
        mounted () {
            this.$nextTick(function () {
                AppVendor()._initTabs();

                if(this.content.data.length == 0){
                    this.$set(this.content,'data', {
                        class: '',
                        type: 'static',
                        route_name: 'module:post.type:dynamic.action:read',
                        total_row: 10,
                        db: [],
                        link: []
                    });
                }

                this.selectValues = $(".libraries-select").select2({
                    allowClear: true
                });
            });
        }
    }
</script>