/**
 * vim: et ts=2 sts=2 sw=2
 * @module     local_cdo_unti2035bas/practice_diaries_control
 */
/* eslint-disable */

import * as DT from 'core_table/dynamic';
import DynamicForm from 'core_form/dynamicform';
import * as practice_controller from 'local_cdo_unti2035bas/local/practice_controller';
import {deleteCancelPromise, saveCancelPromise} from 'core/notification';
import {get_string} from 'core/str';


export default class {
  #uniqId;
  #streamId;
  #tableId;
  #formAdd;
  #elementRoot;
  #elementFormAddContainer;
  #elementTable;

  constructor(uniqId) {
    this.#uniqId = uniqId;
    this.#elementRoot = document.querySelector(`div[data-uniqid="${uniqId}"`);
    this.#streamId = this.#elementRoot.dataset.streamid;
    this.#tableId = this.#elementRoot.dataset.tableid;
    this.#elementTable = document.querySelector(`[data-table-uniqueid="${this.#tableId}"]`);
    this.#elementFormAddContainer = this.#elementRoot.querySelector('.practice-diaries-add-form');
    this.#formAdd = new DynamicForm(this.#elementFormAddContainer, 'local_cdo_unti2035bas\\form\\practice_diaries_add_form');
    this.registerEventListeners();
    this.#formAdd.load({streamid: this.#streamId});
  }

  registerEventListeners() {
    document.addEventListener(DT.Events.tableContentRefreshed, event => {
      if (event.target.dataset.tableUniqueid == this.#tableId) {
        this.#elementTable = event.target;
      }
    });

    this.#elementFormAddContainer.addEventListener(this.#formAdd.events.FORM_SUBMITTED, async() => {
      DT.refreshTableContent(this.#elementTable);
      await this.#formAdd.load({streamid: this.#streamId});
    });

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
  }

  callAction(dataset) {
    if (dataset.action == 'practice-diary-delete') {
      this.practiceDiaryDelete(dataset.practicediaryid);
    } else if (dataset.action == 'statement-send') {
      this.statementSend(dataset.url);
    }
  }
  
  async practiceDiaryDelete(practiceDiaryId) {
    try {
      await deleteCancelPromise(
        get_string('practicediarydeletetitle', 'local_cdo_unti2035bas'),
        get_string('practicediarydeletequestion', 'local_cdo_unti2035bas'),
        get_string('yes'));
    } catch {
      return;
    }
    await practice_controller.practiceDiaryDelete(practiceDiaryId);
    DT.refreshTableContent(this.#elementTable);
  }

  async statementSend(url) {
    try {
      await saveCancelPromise(
        get_string('statementsendtitle', 'local_cdo_unti2035bas'),
        get_string('statementsendquestion', 'local_cdo_unti2035bas'),
        get_string('yes'),
      );
    } catch {
      return;
    }
    window.location.href = url;
  }
}
