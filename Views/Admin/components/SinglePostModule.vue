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
    <div class="edit-post">
        <form class="form" >
            <h5 class="module-title">Information :</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.name" :id="'content-name-' + line">
                        <label :for="'content-name-' + line">Nom *</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <input type="text" class="form-control" v-model="content.block" :id="'content-block-' + line">
                        <label :for="'content-block-' + line">Bloc *</label>
                    </div>
                </div>
            </div>
            <div v-show="auth.status.level < 4">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.category.title" readonly
                                   :id="'content-module-' + line">
                            <label :for="'content-module-' + line">Module</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.name" readonly
                                   :id="'content-extension-' + line">
                            <label :for="'content-extension-' + line">Extension</label>
                        </div>
                    </div>
                </div>
                <div class="form-group" >
                    <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                    <label :for="'content-class-' + line">Class</label>
                </div>
                <h5 class="module-title">Choix du template :</h5>
                <template-editor :id="line" :templates="templates" :template="content.template" label="Template du contenu"></template-editor>
            </div>
            <div>
                <div v-show="auth.status.level < 4">
                    <h5 class="module-title">Configuration avancé :</h5>
                    <div class="form-group" v-if="page != null && 'route' in page && 'url' in page.route">
                        <input type="text" class="form-control" id="page_url" :value="page.route.url" readonly>
                        <div class="form-control-line"></div>
                        <label for="page_url">Page url</label>
                    </div>
                    <br>
                </div>
                <table class="table table-bordered no-margin">
                    <tbody>
                    <tr v-for="(db,i) in content_data.db">
                        <td v-show="auth.status.level < 4" style="width: 5%">{{i}}</td>
                        <td v-show="auth.status.level < 4" style="width: 30%">
                            <div class="form-group">
                                <select :id="'db_table_'+i" v-model="db.alias" class="form-control">
                                    <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                                </select>
                                <label :for="'db_table_'+i">Table</label>
                            </div>
                        </td>
                        <td v-show="auth.status.level < 4" style="width: 30%">
                            <div class="form-group">
                                <select :id="'db_column_'+i" v-model="db.column" class="form-control">
                                    <option v-for="column in columns" :value="column">{{column}}</option>
                                </select>
                                <label :for="'db_column_'+i">Colonne</label>
                            </div>
                        </td>
                        <td>
                            <div v-show="auth.status.level < 4">
                                <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                                    <li :class="classStatic(i)"><a @click="changeType(i, 'static')" href="#content_static">Statique</a>
                                    </li>
                                    <li :class="classDynamic(i)"><a @click="changeType(i, 'dynamic')"
                                                                    href="#content_dynamic">Dynamique</a></li>
                                </ul>
                            </div><!--end .card-head -->
                            <div class="card-body tab-content">
                                <div :class="[classStatic(i), 'tab-pane']" id="content_static">
                                    <p v-show="auth.status.level > 3">Choisissez l'article à afficher sur cette page :</p>
                                    <div class="form-group">
                                        <select :id="'db_value_'+i" @change="setValueId(i,db.alias,db.column,db.value)"
                                                v-model="db.value" class="form-control">
                                            <option v-for="value in getValues(db.alias)" :value="value[db.column]">
                                                {{value.name}}
                                            </option>
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
                        </td>01016
                        <td v-show="auth.status.level < 4" style="width: 5%">
                            <button type="button" @click="removeDbField(i)"
                                    class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button v-show="auth.status.level < 4" type="button" @click="addDbField()"
                        class="btn ink-reaction pull-right btn-floating-action btn-info add-field"><i
                        class="fa fa-plus"></i></button>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary" data-dismiss="modal">Enregistrer</button>
        </div>
    </div>
</template>

<script type="text/babel">

    import TemplateEditor from '../../../../../Blocks/AdminBlock/Front/components/Helper/TemplateEditor.vue'

    import {AppVendor} from '../../../../../Blocks/AdminBlock/Resources/public/js/app'
    import {mapGetters, mapActions} from 'vuex'

    import {template_api} from '../../../../../Blocks/AdminBlock/Front/api'
    import {post_api} from '../api'

    export default{
        name: 'single-post',
        components: {TemplateEditor},
        props: {
            line: {
                default: '0'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
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
                tables: {
                    p: 'Article',
                    c: 'Catégorie'
                },
                columns: ['', 'id', 'slug'],
                values: {
                    c: null,
                    p: null
                },
                templates: []
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
        computed: {
            ...mapGetters([
                'auth'
            ])
        },
        methods: {
            ...mapActions([
                'read'
            ]),
            setValueId(key, table, col, val){
                if (table in this.values && this.values[table] != null) {
                    let index = this.values[table].findIndex((i) => i[col] == val);
                    this.content_data.db[key].value_id = this.values[table][index].id
                }
            },
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
                    route: '',
                    value_id: ''
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
            changeType(i, type){
                this.content_data.db[i].type = type;
            },
            updateContent(){
                this.$emit('updateContent',this.content);
            }
        },
        created () {
            this.read({api: template_api.get_website_content_layouts + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: post_api.list_table_values + this.website}).then((response) => {
                this.values = response.data;
            });
        },
        mounted () {
            if ('db' in this.content.data)this.content_data = this.content.data;

            this.$nextTick(function () {
                AppVendor()._initTabs();
            });
        }
    }
</script>