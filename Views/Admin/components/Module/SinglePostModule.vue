<style>
    .module-title {
        padding: 10px;
        background: #f2f2f2;
    }

    .edit-post-module .add-field {
        margin-top: -20px;
        margin-right: 8px;
    }

</style>

<template>
    <div class="edit-post-module">
        <form class="form">
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
            <div>
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
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                    <label :for="'content-class-' + line">Class</label>
                </div>
                <h5 class="module-title">Choix du template :</h5>
                <template-editor @updateTemplate="updateTemplate" :id="line" :templates="templates" :template="content.template"
                                 label="Template du contenu"></template-editor>
            </div>
            <div>
                <div>
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
                        <td style="width: 5%">{{i}}</td>
                        <td style="width: 30%">
                            <div class="form-group">
                                <select :id="'db_table_'+i" v-model="db.alias" class="form-control">
                                    <option v-for="(table,alias) in tables" :value="alias">{{table}}</option>
                                </select>
                                <label :for="'db_table_'+i">Table</label>
                            </div>
                        </td>
                        <td>
                            <div>
                                <ul class="nav nav-tabs nav-justified" data-toggle="tabs">
                                    <li :class="classStatic(i)"><a @click="changeType(i, 'static')"
                                                                   href="#content_static">Statique</a>
                                    </li>
                                    <li :class="classDynamic(i)"><a @click="changeType(i, 'dynamic')"
                                                                    href="#content_dynamic">Dynamique</a></li>
                                </ul>
                            </div><!--end .card-head -->
                            <div class="card-body tab-content">
                                <div :class="[classStatic(i), 'tab-pane']" id="content_static">
                                    <select2 v-if="getValues(db.alias) instanceof Array && getValues(db.alias).length > 0"
                                            @updateValue="updateDbValue" :updateParams="{key: i}"
                                            :contents="getValues(db.alias)" :id="'select-' + line + '-' + i"
                                            val_index="id" index="name" label="Valeur"
                                            :val="db.value"></select2>
                                </div>
                                <div :class="[classDynamic(i), 'tab-pane']" id="content_dynamic">
                                    <div class="form-group">
                                        <input type="text" v-model="db.route" class="form-control" :id="'db_route_'+i">
                                        <label :for="'db_route_'+i">Route</label>
                                    </div>
                                    <div class="form-group">
                                        <select :id="'db_column_'+i" v-model="db.column" class="form-control">
                                            <option v-for="column in columns" :value="column">{{column}}</option>
                                        </select>
                                        <label :for="'db_column_'+i">Colonne</label>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="width: 5%">
                            <button type="button" @click="removeDbField(i)"
                                    class="btn ink-reaction btn-floating-action btn-danger"><i class="fa fa-times"></i>
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <button type="button" @click="addDbField()"
                        class="btn ink-reaction pull-right btn-floating-action btn-info add-field"><i
                        class="fa fa-plus"></i></button>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary">Enregistrer</button>
        </div>
    </div>
</template>

<script type="text/babel">

    import {AppVendor} from '@admin/js/app'
    import {mapActions} from 'vuex'

    import {template_api} from '@front/api'
    import {post_api} from '../../api'

    import module_mixin from '@front/mixin/module'

    export default{
        name: 'single-post',
        components: {
            TemplateEditor: resolve => {
                require(['@front/components/Helper/TemplateEditor.vue'], resolve)
            },
            Select2: resolve => {
                require(['@front/components/Helper/Select2.vue'], resolve)
            }
        },
        mixins: [module_mixin],
        props: {
            line: {
                default: 'default'
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
        methods: {
            ...mapActions([
                'read', 'setResponse'
            ]),
            updateDbValue(val, oldVal, params){
                this.content_data.db[params.key].value = val;
            },
            updateTemplate(template){
                if (this.content.template !== undefined) this.content.template = template;
            },
            classStatic (i) {
                return {
                    active: (this.content_data.db[i] !== undefined && this.content_data.db[i]['type'] !== undefined && this.content_data.db[i].type == 'static')
                }
            },
            classDynamic (i) {
                return {
                    active: (this.content_data.db[i] !== undefined && this.content_data.db[i]['type'] !== undefined && this.content_data.db[i].type == 'dynamic')
                }
            },
            addDbField(){
                this.content_data.db.push({
                    alias: '',
                    type: 'static',
                    column: '',
                    route: '',
                    value: []
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
            if (this.content.data != null && this.content.data.db !== undefined)this.content_data = this.content.data;

            this.$nextTick(function () {
                AppVendor()._initTabs();
            });
        }
    }
</script>