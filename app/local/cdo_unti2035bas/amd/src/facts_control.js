/**
 * vim: et ts=2 sts=2 sw=2
 * @module local_cdo_unti2035bas/facts_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import ModalFormFactEdit from 'local_cdo_unti2035bas/local/modalform_fact_edit';
import {decorate} from 'local_cdo_unti2035bas/utils';
import {get_loading_deco} from 'local_cdo_unti2035bas/loading';
import {get_string} from 'core/str';
import {deleteCancelPromise, saveCancelPromise} from 'core/notification';
import * as fd_controller from 'local_cdo_unti2035bas/local/fd_controller';


export default class {
  #uniqId;
  #tableId;
  #factdefId;
  #actorUntiId;
  #elementRoot;
  #elementTable;
  #elementSelectStudent;
  #elementButtonCreate;

  constructor(uniqId, tableId) {
    this.#uniqId = uniqId;
    this.#tableId = tableId;
    this.#elementRoot = document.querySelector(`[data-uniqid="${this.#uniqId}"]`);
    this.#elementTable = document.querySelector(`[data-table-uniqueid="${this.#tableId}"]`);
    this.#elementSelectStudent = this.#elementRoot.querySelector('[name="student"]');
    this.#elementButtonCreate = this.#elementRoot.querySelector('.create-fact');
    this.#factdefId = Number(this.#elementRoot.dataset.factdefid);
    this.#actorUntiId = Number(this.#elementSelectStudent.value);
    this.#elementSelectStudent.value = this.#elementSelectStudent.querySelector('option[selected]').value;
    this.registerDecorators();
    this.registerEventListeners();
    this.stopLoading();
  }

  registerDecorators() {
    const loading = get_loading_deco(this.#uniqId, this);
    decorate.call(this, this.update, loading);
    decorate.call(this, this.showCreateModal, loading);
    decorate.call(this, this.factDelete, loading);
  }

  registerEventListeners() {
    document.addEventListener(DT.Events.tableContentRefreshed, event => {
      if (event.target.dataset.tableUniqueid == this.#tableId) {
        this.#elementTable = event.target;
      }
    });

    this.#elementRoot.addEventListener('change', () => this.update());

    this.#elementButtonCreate.addEventListener('click', () => this.showCreateModal());

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

  async update() {
    this.#actorUntiId = Number(this.#elementSelectStudent.value);
    await DT.setFilters(this.#elementTable,
      {
        jointype: 1,
        filters: [
          {name: 'factdefid', values: [this.#factdefId], jointype: 1},
          {name: 'actoruntiid', values: [this.#actorUntiId], jointype: 1},
        ],
      },
      true,
    );
  }

  async showCreateModal() {
    if (!this.#actorUntiId) {
      return;
    }
    const modal = new ModalFormFactEdit(this.#factdefId, this.#actorUntiId);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => this.update());
    await modal.show();
  }

  async showEditModal(factId) {
    if (!this.#actorUntiId) {
      return;
    }
    const modal = new ModalFormFactEdit(this.#factdefId, this.#actorUntiId, factId);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => this.update());
    await modal.show();
  }

  callAction(dataset) {
    if (dataset.action == 'fact-delete') {
      this.factDelete(dataset.factid);
    } else if (dataset.action == 'entity-edit' && dataset.object_ == 'fact_entity') {
      this.showEditModal(dataset.objectid);
    } else if (dataset.action == 'statement-send') {
      this.statementSend(dataset.url);
    }
  }

  async factDelete(factId) {
    try {
      await deleteCancelPromise(
        get_string('factdeletetitle', 'local_cdo_unti2035bas'),
        get_string('factdeletequestion', 'local_cdo_unti2035bas'),
        get_string('yes'));
    } catch {
      return;
    }
    await fd_controller.factDelete(factId);
    this.update();
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

  startLoading() {
    this.#elementRoot.disabled = true;
  }

  stopLoading() {
    this.#elementRoot.disabled = false;
  }
}
