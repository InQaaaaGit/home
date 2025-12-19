/**
 * vim: et ts=2 sts=2 sw=2
 * @module     local_cdo_unti2035bas/fact_edit_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import {get_string} from 'core/str';
import DynamicForm from 'core_form/dynamicform';
import {deleteCancelPromise} from 'core/notification';
import * as fd_controller from 'local_cdo_unti2035bas/local/fd_controller';


export default class {
  #factId;
  #tableId;
  #elementFormAddContainer;
  #elementTable;
  #formAdd;

  constructor(factId, tableId) {
    this.#factId = factId;
    this.#tableId = tableId;
    this.#elementFormAddContainer = document.querySelector(`div#container-fact-edit-add-ext-form-${factId}`);
    this.#elementTable = document.querySelector(`[data-table-uniqueid="${tableId}"]`);
    this.#formAdd = new DynamicForm(this.#elementFormAddContainer, 'local_cdo_unti2035bas\\form\\fact_add_extension_form');
    this.registerEventListeners();
    this.#formAdd.load({factid: this.#factId});
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
      await this.#formAdd.load({factid: this.#factId});
    });
  }

  callAction(dataset) {
    if (dataset.action == 'fact-extension-delete') {
      this.factExtensionDelete(dataset.factid, dataset.extensionname);
    }
  }

  async factExtensionDelete(factId, extensionName) {
    try {
      await deleteCancelPromise(
        get_string('fdextensiondeletetitle', 'local_cdo_unti2035bas'),
        get_string('fdextensiondeletequestion', 'local_cdo_unti2035bas', extensionName),
        get_string('yes'));
    } catch {
      return;
    }
    await fd_controller.factExtensionDelete(factId, extensionName);
    DT.refreshTableContent(this.#elementTable);
    this.#formAdd.load({factid: this.#factId});
  }
}
