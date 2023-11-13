<script lang="ts">
import { defineComponent } from 'vue';
import {
  AjaxHelper,
  ContentBlock,
  ContentTable,
  NotificationsStore,
  Periods,
  translate,
} from 'CoreHome';
import { Field } from 'CorePluginsAdmin';

interface IndexationStatus {
  id_archive: number;
  name: string;
  id_site: number;
  period_begin_date: string;
  period_end_date: string;
  period: string;
  ts_archived: string;
  value: string;
}

interface Segment {
  definition: string,
  name: string,
}

interface Option {
  key: string | number;
  value: string;
}

interface IndexationViewerState {
  page: number;
  isLoading: boolean;
  statuses: Option[];
  selectedStatus: string;
  segments: Option[];
  selectedSegment: string;
  indexationStatuses?: IndexationStatus[];
}

export default defineComponent({
  // State --------------------------------------------------------------------
  data(): IndexationViewerState {
    return {
      page: 1,
      isLoading: true,
      statuses: [
        { key: '', value: translate('IndexationViewer_AnyStatus') },
        { key: 'DONE_OK', value: translate('IndexationViewer_StatusDoneOK') },
        { key: 'DONE_ERROR', value: translate('IndexationViewer_StatusDoneError') },
        { key: 'DONE_INVALIDATED', value: translate('IndexationViewer_StatusDoneInvalidated') },
      ],
      selectedStatus: '',
      segments: [],
      selectedSegment: '',
      indexationStatuses: [],
    };
  },
  props: {
    limitPerPage: {
      type: Number,
      default: 15,
    },
  },
  // computed: {},
  methods: {
    fetchSegments() {
      const params = {
        method: 'IndexationViewer.getSegmentOptions',
      };

      AjaxHelper.fetch<Segment[]>(params)
        .catch((error: Error | string) => {
          NotificationsStore.show({
            message: (error as Error).message || error as string,
            context: 'error',
            type: 'transient',
          });
        })
        .then((segments) => {
          if (Array.isArray(segments)) {
            this.segments = segments.map((segment) => ({
              key: segment.definition,
              value: segment.name,
            }));
          }
        });
    },

    update() {
      this.isLoading = true;

      this.indexationStatuses = [];
      this.getIndexationStatuses({
        segment: this.selectedSegment,
        status: this.selectedStatus,
        page: this.page,
        limitPerPage: this.limitPerPage,
      }).catch((error: Error | string) => {
        NotificationsStore.show({
          message: (error as Error).message || error as string,
          context: 'error',
          type: 'transient',
        });

        return [];
      }).then((indexationStatuses) => {
        this.indexationStatuses = indexationStatuses;
      }).finally(() => {
        this.isLoading = false;
      });
    },

    onSelectSegment(event: string) {
      this.selectedSegment = event;
      this.update();
    },

    onSelectStatus(event: string) {
      this.selectedStatus = event;
      this.update();
    },

    onSearchKeydown(event: KeyboardEvent) {
      if (event.code !== 'Enter') {
        return;
      }

      this.update();
    },

    previousPage() {
      if (this.page > 0) {
        this.page -= 1;
        this.update();
      }
    },

    nextPage() {
      if (this.indexationStatuses?.length) {
        this.page += 1;
        this.update();
      }
    },

    prettyPrintPeriod(indexationStatus: IndexationStatus): string {
      return Periods.parse(indexationStatus.period, indexationStatus.period_begin_date)
        .getPrettyString();
    },
  },
  // watch: {},
  // emits: {},
  // expose: [],

  // Lifecycle ----------------------------------------------------------------
  setup() {
    let fetchIndexationStatusesAbort: AbortController | null = null;

    const getIndexationStatuses = (queryParams: QueryParameters) => {
      if (fetchIndexationStatusesAbort) {
        fetchIndexationStatusesAbort.abort();
        fetchIndexationStatusesAbort = null;
      }

      fetchIndexationStatusesAbort = new AbortController();

      const params = {
        ...queryParams,
        method: 'IndexationViewer.getIndexationStatuses',
      };

      const options = {
        abortController: fetchIndexationStatusesAbort,
        createErrorNotification: false,
      };

      return AjaxHelper.fetch<IndexationStatus[]>(params, options).finally(() => {
        fetchIndexationStatusesAbort = null;
      });
    };

    return {
      getIndexationStatuses,
    };
  },
  // beforeCreate() {},
  created() {
    this.fetchSegments();
  },
  // beforeMount() {},
  mounted() {
    this.update();
  },
  // beforeUpdate() {},
  // updated() {},
  // beforeUnmount() {},
  // unmounted() {},
  // errorCaptured() {},
  // renderTracked() {},
  // renderTriggered() {},
  // activated() {},
  // deactivated() {},
  // serverPrefetch() {},

  // Composition --------------------------------------------------------------
  // provide: {},
  // inject: {},
  // mixins: [mixin]
  // extends: Base,

  // Misc ---------------------------------------------------------------------
  name: 'IndexationViewer',
  // inheritAttrs: true,
  components: {
    ContentBlock,
    Field,
  },
  directives: {
    ContentTable,
  },
});
</script>

<template>
    <div>
        <ContentBlock :content-title="translate('IndexationViewer_IndexTitle')">
            <div class="row">
                <div>
                    <div class="segments col s4 m3 l2">
                        <Field :title="translate('IndexationViewer_SelectSegment')"
                               uicontrol="select"
                               name="selectedSegment"
                               :model-value="selectedSegment"
                               @update:model-value="onSelectSegment($event)"
                               :full-width="true"
                               :options="segments"
                        />
                    </div>
                    <div class="statuses col s4 m3 l2">
                        <Field :title="translate('IndexationViewer_SelectStatus')"
                               uicontrol="select"
                               name="selectedStatus"
                               :model-value="selectedStatus"
                               @update:model-value="onSelectStatus($event)"
                               :full-width="true"
                               :options="statuses"
                        />
                    </div>
                </div>

                <table v-content-table>
                    <thead>
                    <tr>
                        <th class="index">{{ translate('General_Id') }}</th>
                        <th class="name">{{ translate('General_Name') }}</th>
                        <th>{{ translate('IndexationViewer_Period') }}</th>
                        <th>{{ translate('IndexationViewer_IndexedAt') }}</th>
                        <th>{{ translate('IndexationViewer_Status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr class="indexationViewer-table-row"
                        v-for="(indexationStatus, index) in indexationStatuses"
                        :key="index"
                    >
                        <td class="index">{{ indexationStatus.id_archive }}</td>
                        <td class="name">{{ indexationStatus.name }}</td>
                        <td>{{ prettyPrintPeriod(indexationStatus) }}</td>
                        <td>{{ indexationStatus.ts_archived }}</td>
                        <td>{{ indexationStatus.value }}</td>
                    </tr>

                    <tr v-if="!isLoading && indexationStatuses?.length === 0">
                        <td colspan="4">
                            {{ translate('General_SearchNoResults') }}
                        </td>
                    </tr>

                    <tr v-show="isLoading">
                        <td colspan="5">
                            <span class="loadingPiwik">
                                <img src="/plugins/Morpheus/images/loading-blue.gif" alt=""/>
                                {{ translate('General_Loading') }}
                            </span>
                        </td>
                    </tr>

                    <tr v-show="page > 1 || indexationStatuses?.length === limitPerPage">
                        <td colspan="5" class="paging">
                            <span class="previous" @click="previousPage()"
                                  style="padding-right: 1rem"
                                  v-show="page > 1"
                            >
                                <a class="btn" @click.prevent="">
                                    <span>&#xAB; {{ translate('General_Previous') }}</span>
                                </a>
                            </span>
                            <span class="next" @click="nextPage()"
                                  v-show="indexationStatuses?.length === limitPerPage"
                            >
                                <a class="btn" @click.prevent="">
                                    <span>{{ translate('General_Next') }} &#xBB;</span>
                                </a>
                            </span>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </ContentBlock>
    </div>
</template>
