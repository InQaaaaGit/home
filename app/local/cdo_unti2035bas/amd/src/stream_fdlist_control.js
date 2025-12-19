/**
 * vim: et ts=2 sts=2 sw=2
 * @module     local_cdo_unti2035bas/stream_fdlist_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import {get_string} from 'core/str';
import DynamicForm from 'core_form/dynamicform';
import {deleteCancelPromise} from 'core/notification';
import * as fd_controller from 'local_cdo_unti2035bas/local/fd_controller';


export default class {
    #streamId;
    #tableId;
    #elementFormAddContainer;
    #elementTable;
    #formAdd;

    constructor(streamId, tableId) {
        this.#streamId = streamId;
        this.#tableId = tableId;
        this.#elementFormAddContainer = document.querySelector(`div#container-stream-fdlist-form-${streamId}`);
        this.#elementTable = document.querySelector(`[data-table-uniqueid="${tableId}"]`);
        this.#formAdd = new DynamicForm(this.#elementFormAddContainer, 'local_cdo_unti2035bas\\form\\stream_fdlist_form');
        this.registerEventListeners();
        this.#formAdd.load({streamid: this.#streamId});
    }

    registerEventListeners() {
      document.addEventListener('click', event => {
        if (!this.#elementTable.contains(event.target)) {
          return;
        }
        const actionLink = event.target.closest('[data-action]');
        if (!actionLink) {
          return;
        }
        event.preventDefault();
        this.callAction(actionLink.dataset);
      });

      document.addEventListener(DT.Events.tableContentRefreshed, event => {
        if (event.target.dataset.tableUniqueid == this.#tableId) {
          this.#elementTable = event.target;
        }
      });

      this.#elementFormAddContainer.addEventListener(this.#formAdd.events.FORM_SUBMITTED, async() => {
        DT.refreshTableContent(this.#elementTable);
        await this.#formAdd.load({streamid: this.#streamId});
      });
    }

    callAction(dataset) {
      if (dataset.action == 'stream-fd-delete') {
        this.streamExtensionDelete(dataset.streamid, dataset.extensionname);
      }
    }

    async streamExtensionDelete(streamId, extensionName) {
      try {
        await deleteCancelPromise(
          get_string('fdextensiondeletetitle', 'local_cdo_unti2035bas'),
          get_string('fdextensiondeletequestion', 'local_cdo_unti2035bas', extensionName),
          get_string('yes'));
      } catch {
        return;
      }
      await fd_controller.streamFDDelete(streamId, extensionName);
      DT.refreshTableContent(this.#elementTable);
      this.#formAdd.load({streamid: this.#streamId});
    }
}
