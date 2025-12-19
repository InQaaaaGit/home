/**
 * vim: et ts=2 sts=2 sw=2
 * @module     local_cdo_unti2035bas/factdef_edit_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import {get_string} from 'core/str';
import DynamicForm from 'core_form/dynamicform';
import {deleteCancelPromise} from 'core/notification';
import * as fd_controller from 'local_cdo_unti2035bas/local/fd_controller';


export default class {
  #factdefId;
  #tableId;
  #elementFormAddContainer;
  #elementTable;
  #formAdd;

  constructor(factdefId, tableId) {
    this.#factdefId = factdefId;
    this.#tableId = tableId;
    this.#elementFormAddContainer = document.querySelector(`div#container-factdef-edit-add-ext-form-${factdefId}`);
    this.#elementTable = document.querySelector(`[data-table-uniqueid="${tableId}"]`);
    this.#formAdd = new DynamicForm(this.#elementFormAddContainer, 'local_cdo_unti2035bas\\form\\factdef_add_extension_form');
    this.registerEventListeners();
    this.#formAdd.load({factdefid: this.#factdefId});
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
      await this.#formAdd.load({factdefid: this.#factdefId});
    });
  }

  callAction(dataset) {
    if (dataset.action == 'factdef-extension-delete') {
      this.factdefExtensionDelete(dataset.factdefid, dataset.extensionname);
    }
  }

  async factdefExtensionDelete(factdefId, extensionName) {
    try {
      await deleteCancelPromise(
        get_string('fdextensiondeletetitle', 'local_cdo_unti2035bas'),
        get_string('fdextensiondeletequestion', 'local_cdo_unti2035bas', extensionName),
        get_string('yes'));
    } catch {
      return;
    }
    await fd_controller.factdefExtensionDelete(factdefId, extensionName);
    DT.refreshTableContent(this.#elementTable);
    this.#formAdd.load({factdefid: this.#factdefId});
  }
}
