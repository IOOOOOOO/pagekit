<template>
    <div>
        <div v-if="!source" :class="[inputClass, 'uk-inline-clip']" @click.prevent="pick">
            <a>
                <div class="uk-placeholder uk-text-center uk-margin-remove">
                    <img width="60" height="60" :alt="'Placeholder Image' | trans" :src="$url('app/system/assets/images/placeholder-image.svg')">
                    <p class="uk-text-muted uk-margin-small-top">{{ title | trans }}</p>
                </div>
            </a>
        </div>

        <div v-else :class="[inputClass, 'uk-inline-clip uk-position-relative uk-transition-toggle uk-visible-toggle']">
            <img :src="source.indexOf('blob:') !== 0 ? $url(source) : source">

            <a class="uk-transition-fade uk-position-cover pk-thumbnail-overlay uk-flex uk-flex-center uk-flex-middle" @click.prevent="pick" />

            <div class="uk-card-badge pk-panel-badge uk-invisible-hover">
                <ul class="uk-subnav pk-subnav-icon">
                    <li>
                        <a class="uk-icon-link" uk-icon="trash" :title="'Delete' | trans" uk-tooltip="delay: 500" @click.prevent="remove" v-confirm="'Reset image?'"></a>
                    </li>
                </ul>
            </div>
        </div>

        <div v-if="inputField" class="uk-margin-small-top">
            <div :class="[inputClass, 'uk-inline']">
                <a class="uk-form-icon" uk-icon="icon: image" @click.prevent="pick" />
                <a v-if="source" v-confirm="'Reset image?'" class="uk-form-icon uk-form-icon-flip" uk-icon="icon: close" @click.prevent="source=''" />
                <input v-model="source" type="text" class="uk-input">
            </div>
        </div>

        <v-modal ref="modal" large :options="{bgClose: false}">
            <panel-finder ref="finder" :root="storage" :modal="true" @select:finder="selectFinder" />

            <div class="uk-modal-footer">
                <div class="uk-flex uk-flex-middle uk-flex-between">
                    <div>
                        <div v-if="isFinder">
                            <span v-if="!finder.selected.length" class="uk-text-meta">{{ '{0} %count% Files|{1} %count% File|]1,Inf[ %count% Files' | transChoice(finder.count, {count: finder.count}) }}</span>
                            <span v-else class="uk-text-meta">{{ '{1} %count% File selected|]1,Inf[ %count% Files selected' | transChoice(finder.selected.length, {count:finder.selected.length}) }}</span>
                        </div>
                    </div>
                    <div>
                        <button class="uk-button uk-button-text uk-margin-right uk-modal-close" type="button">
                            {{ 'Cancel' | trans }}
                        </button>
                        <button class="uk-button uk-button-primary" type="button" :disabled="!choice" @click.prevent="select">
                            {{ 'Select' | trans }}
                        </button>
                    </div>
                </div>
            </div>
        </v-modal>
    </div>
</template>

<script>

export default {

    props: {
        inputClass: { default: '' },
        value: { default: '' },
        title: { default: 'Select Image' },
        inputField: { default: true, type: Boolean },
    },

    data() {
        return _.merge({
            choice: '',
            source: this.value,
            finder: {},
        }, $pagekit);
    },

    computed: {
        isFinder() {
            return !!((this.finder.hasOwnProperty('selected') && this.finder.selected));
        },
    },

    mounted() {
        const vm = this;
        UIkit.util.on(this.$refs.modal.$el, 'shown', () => {
            vm.finder = vm.$refs.finder;
        });
    },

    methods: {
        selectFinder(val) {
            this.choice = this.hasSelection();
        },

        pick() {
            this.$refs.modal.open();
        },

        select() {
            const old_source = this.source;
            this.source = decodeURI(this.$refs.finder.getSelected()[0]);
            this.$emit('input', this.source);
            this.$emit('image:selected', this.source, old_source);
            this.$refs.finder.removeSelection();
            this.$refs.modal.close();
        },

        remove() {
            this.source = '';
            // this.$dispatch('image-removed');
            this.$emit('image:removed');
        },

        hasSelection() {
            const selected = this.$refs.finder.getSelected();
            return selected.length === 1 && this.$refs.finder.isImage(selected[0]);
        },
    },

    watch: {
        source(src) {
            this.$emit('input', src);
        },
    },

};

Vue.component('input-image', (resolve, reject) => {
    Vue.asset({
        js: [
            'app/system/modules/finder/app/bundle/panel-finder.js',
        ],
    }).then(() => {
        resolve(require('./input-image.vue'));
    });
});

</script>
