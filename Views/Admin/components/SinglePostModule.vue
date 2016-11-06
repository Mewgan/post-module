<style>
    .module-title{
        padding: 10px;
        background: #f2f2f2;
    }
    .edit-post .add-field{
        margin-top: -20px;
        margin-right: 8px;
    }
</style>

<template>
    <form class="form edit-post">
        <loading :loading="loading"></loading>
        <h5 class="module-title">Information :</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content.name" id="content_name">
                    <label for="content_name">Nom *</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content.block" id="content_block">
                    <label for="content_block">Bloc *</label>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" :value="content.module.category.title" readonly id="content_module">
                    <label for="content_module">Module</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <input type="text" class="form-control" :value="content.module.name" readonly id="content_extension">
                    <label for="content_extension">Extension</label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <input type="text" class="form-control" v-model="content_data.class" id="content_class">
            <label for="content_class">Class</label>
        </div>
        <h5 class="module-title">Choix du template :</h5>
        <div class="form-group">
            <select id="content_template" v-model="content.template.id" class="form-control">
                <option v-for="template in templates" :value="template.id">{{template.title}}</option>
            </select>
            <label for="content_template">Template du contenu</label>
        </div>
        <div>
            <h5 class="module-title">Configuration avancé :</h5>
            <div class="form-group">
                <input type="text" class="form-control" id="page_url" :value="page.route.url" readonly><div class="form-control-line"></div>
                <label for="page_url">Page url</label>
            </div>
            <br>
            <table class="table table-bordered no-margin">
                <tbody>
                <tr v-for="(db,i) in content_data.db">
                    <td style="width: 5%">{{i}}</td>
                    <td style="width: 30%">
                        <div class="form-group">
                            <select :id="'db_table_'+i" v-model="db.alias" class="form-control">
                                <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                            </select>
                            <label :for="'db_table_'+i">Table</label>
                        </div>
                    </td>
                    <td style="width: 30%">
                        <div class="form-group">
                            <select :id="'db_column_'+i" v-model="db.column" class="form-control">
                                <option v-for="column in columns" :value="column">{{column}}</option>
                            </select>
                            <label :for="'db_column_'+i">Colonne</label>
                        </div>
                    </td>
                    <td style="width: 30%">
                        <div>
                            <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                                <li :class="classStatic(i)"><a @click="changeType(i, 'static')" href="#content_static">Statique</a></li>
                                <li :class="classDynamic(i)"><a @click="changeType(i, 'dynamic')" href="#content_dynamic">Dynamique</a></li>
                            </ul>
                        </div><!--end .card-head -->
                        <div class="card-body tab-content">
                            <div :class="[classStatic(i), 'tab-pane']" id="content_static">
                                <div class="form-group">
                                    <select :id="'db_value_'+i" v-model="db.value" class="form-control">
                                        <option v-for="value in getValues(db.alias)" :value="value[db.column]">{{value.name}}</option>
                                    </select>
                                    <label :for="'db_value_'+i">Valeur</label>
                                </div>
                            </div>
                            <div :class="[classDynamic(i), 'tab-pane']" id="content_dynamic">
                                <div class="form-group">
                                    <input type="text" v-model="db.route" class="form-control" :id="'db_route_'+i">
                                    <label :for="'db_route_'+i">Route</label>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td style="width: 5%">
                        <button type="button" @click="removeDbField(i)" class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i></button>
                    </td>
                </tr>
                </tbody>
            </table>
            <button type="button" @click="addDbField()" class="btn ink-reaction pull-right btn-floating-action btn-info add-field"><i class="fa fa-plus"></i></button>
        </div>
    </form>
</template>

<script type="text/babel">

    import {AppVendor} from '../../../../../Blocks/AdminBlock/Resources/public/js/app'
    import Loading from '../../../../../Blocks/AdminBlock/Front/components/Helper/Loading.vue'
    import {mapActions} from 'vuex'

    export default{
        name: 'single-post',
        components: {Loading},
        props: {
            line: {
                default: '0'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                type: Object,
                required: true
            },
            website: {
                required: true
            }
        },
        data(){
            return {
                content_data: {
                    class: '',
                    db: []
                },
                tables : {
                    p: 'Article',
                    c: 'Catégorie'
                },
                columns: ['','id','slug'],
                values: {
                    c: null,
                    p: null
                },
                templates: [],
                loading: false
            }
        },
        watch: {
            'content_data': {
                handler(){
                    this.$set(this.content, 'data', this.content_data);
                },
                deep: true
            }
        },
        methods: {
            ...mapActions([
                'read'
            ]),
            classStatic (i) {
                return {
                    active: (i in this.content_data.db && 'type' in this.content_data.db[i] && this.content_data.db[i].type == 'static')
                }
            },
            classDynamic (i) {
                return {
                    active: (i in this.content_data.db && 'type' in this.content_data.db[i] && this.content_data.db[i].type == 'dynamic')
                }
            },
            addDbField(){
                this.content_data.db.push({
                    alias: '',
                    type: 'static',
                    column: '',
                    value: '',
                    route: ''
                });
                this.$nextTick(function () {
                    AppVendor()._initTabs();
                });
            },
            removeDbField(index){
                this.content_data.db.splice(index, 1);
            },
            getValues(table){
                return this.values[table];
            },
            changeType(i,type){
                this.content_data.db[i].type = type;
            }
        },
        created () {
            this.loading = true;
            this.read({api: ADMIN_DOMAIN + '/template/get-website-content-layouts/' + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: ADMIN_DOMAIN + '/module/post/list-table-values/' + this.website}).then((response) => {
                this.values = response.data;
                this.loading = false;
            });
        },
        mounted () {
            if('db' in this.content.data)this.content_data = this.content.data;

            this.$nextTick(function () {
                AppVendor()._initTabs();
            });
        }
    }
</script>