<template>
    <v-card>
        <ValidationObserver
            v-slot="{ invalid }"
            tag="form"
            @submit.prevent="save"
        >
            <v-card-title>
                <span class="headline">{{ $t(formTitle) }}</span>
            </v-card-title>

            <v-card-text>
                <v-container grid-list-md>
                    <v-row
                        v-if="isValid"
                    >
                        <v-flex
                            v-for="form in forms"
                            :key="form.name"
                            xs12
                            sm6
                            md4
                        >
                            <ValidationProvider
                                v-slot="{ errors }"
                                :rules="form.validate"
                                :name="form.text"
                            >
                                <FormItem
                                    v-model="formInputs[form.name]"
                                    :form="form"
                                    :detail="detail"
                                    :form-inputs="formInputs"
                                    :validate-errors="errors"
                                    :increment="increment"
                                />
                            </ValidationProvider>
                        </v-flex>
                    </v-row>
                </v-container>
            </v-card-text>

            <v-card-actions>
                <v-spacer />
                <v-btn
                    color="blue darken-1"
                    text
                    @click="close"
                >
                    {{ $t('misc.cancel') }}
                </v-btn>
                <v-btn
                    :disabled="invalid"
                    color="blue darken-1"
                    text
                    type="submit"
                    @click="save"
                >
                    {{ $t('misc.save') }}
                </v-btn>
            </v-card-actions>
        </ValidationObserver>
    </v-card>
</template>

<script>
    import { mapGetters, mapActions } from 'vuex';
    import { get, set } from 'lodash';
    import FormItem from '../molecules/FormItem';

    export default {
        components: {
            FormItem,
        },
        props: {
            targetModel: {
                type: String,
                required: true,
            },
            targetId: {
                required: true,
                validator: prop => null === prop || 'number' === typeof prop,
            },
            increment: {
                type: Number,
                required: true,
                submitting: false,
            },
        },
        data () {
            return {
                formInputs: {},
                nowModel: null,
            };
        },
        computed: {
            ...mapGetters({
                model: 'crud/getTargetModel',
                getModelForms: 'getModelForms',
                getDetailData: 'crud/getDetailData',
            }),
            formTitle () {
                return null === this.targetId ? 'misc.new_item' : 'misc.edit_item';
            },
            forms () {
                return this.getModelForms(this.model);
            },
            detail () {
                return this.targetId ? (this.getDetailData || {}) : {};
            },
            sendInputs () {
                const inputs = {};
                const filtered = (name, value, form) => name in form && 'function' === typeof form[ name ] ? form[ name ](value, this.formInputs, form) : value;
                this.forms.map(form => {
                    set(inputs, filtered('nameFilter', form.name, form), filtered('valueFilter', this.formInputs[ form.name ], form));
                });
                return inputs;
            },
            isValid () {
                return this.targetModel === this.nowModel;
            },
        },
        watch: {
            increment: {
                handler: 'setup',
                immediate: true,
            },
        },
        methods: {
            ...mapActions({
                setModel: 'crud/setModel',
                setDetail: 'crud/setDetail',
                create: 'crud/create',
                edit: 'crud/edit',
            }),
            async setup () {
                this.clearForm();
                if (!await this.getDetail()) {
                    return;
                }

                this.fillForm();
                this.nowModel = this.targetModel;
            },
            clearForm () {
                this.formInputs = {};
                this.forms.map(form => {
                    this.formInputs[ form.name ] = get(form, 'default', '');
                });
            },
            async getDetail () {
                await this.setModel(this.targetModel);
                if (this.targetId) {
                    await this.setDetail(this.targetId);
                    if (!this.getDetailData) {
                        this.close();
                        return false;
                    }
                }
                return true;
            },
            fillForm () {
                const inputs = {};
                this.forms.map(form => {
                    inputs[ form.name ] = get(this.detail, form.value, get(form, 'default', ''));
                });
                this.formInputs = inputs;
            },
            update (form, value) {
                this.formInputs[ form.name ] = value;
            },
            async save () {
                if (this.submitting) {
                    return;
                }
                this.submitting = true;
                if (this.targetId) {
                    if (await this.edit({ model: this.model, id: this.targetId, data: this.sendInputs })) {
                        this.close();
                    }
                } else {
                    if (await this.create({ model: this.model, data: this.sendInputs })) {
                        this.close();
                    }
                }
                this.submitting = false;
            },
            close () {
                this.$emit('close-edit');
            },
        },
    };
</script>
