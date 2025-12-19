/**
 * vim: et ts=2 sts=2 sw=2
 * @module local_cdo_unti2035bas/actions_control
 */
/* eslint-disable jsdoc/require-jsdoc */

import * as DT from 'core_table/dynamic';
import {get_string} from 'core/str';
import {saveCancel} from 'core/notification';
import ModalFormModuleEdit from 'local_cdo_unti2035bas/local/modalform_module_edit';
import ModalFormStreamEdit from 'local_cdo_unti2035bas/local/modalform_stream_edit';
import ModalFormThemeEdit from 'local_cdo_unti2035bas/local/modalform_theme_edit';
import ModalFormActivityEdit from 'local_cdo_unti2035bas/local/modalform_activity_edit';
import ModalFormAssessmentEdit from 'local_cdo_unti2035bas/local/modalform_assessment_edit';
import ModalFormFactdefEdit from 'local_cdo_unti2035bas/local/modalform_factdef_edit';
import * as stream_controller from 'local_cdo_unti2035bas/local/stream_controller';


function init(uniqid) {
  let table = document.querySelector(`[data-table-uniqueid="${uniqid}"]`);
  document.addEventListener(DT.Events.tableContentRefreshed, event => {
    if (event.target.dataset.tableUniqueid == uniqid) {
      table = event.target;
    }
  });

  async function show_modal_stream_edit(id) {
    const modal = new ModalFormStreamEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function show_modal_module_edit(id) {
    const modal = new ModalFormModuleEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function show_modal_theme_edit(id) {
    const modal = new ModalFormThemeEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function show_modal_activity_edit(id) {
    const modal = new ModalFormActivityEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function show_modal_assessment_edit(id) {
    const modal = new ModalFormAssessmentEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function show_modal_factdef_edit(id) {
    const modal = new ModalFormFactdefEdit(id);
    modal.addEventListener(modal.events.FORM_SUBMITTED, () => {
      DT.refreshTableContent(table);
    });
    await modal.show();
    return modal;
  }

  async function stream_sync(streamId) {
    await stream_controller.stream_sync(streamId);
    DT.refreshTableContent(table);
  }

  async function statement_send(actionurl) {
    saveCancel(
      get_string('statementsendtitle', 'local_cdo_unti2035bas'),
      get_string('statementsendquestion', 'local_cdo_unti2035bas'),
      get_string('yes'),
      () => {window.location.href = actionurl;},
    );
  }

  async function statement_cancel(lrid, actionurl) {
    saveCancel(
      get_string('cancelconfirmtitle', 'local_cdo_unti2035bas'),
      get_string('cancelconfirmquestion', 'local_cdo_unti2035bas', lrid),
      get_string('yes'),
      () => {window.location.href = actionurl;},
    );
  }

  function call_action(dataset) {
    if (dataset.action == 'entity-edit') {
      if (dataset.object_ == 'stream_entity') {
        show_modal_stream_edit(dataset.objectid);
      }
      if (dataset.object_ == 'module_entity') {
        show_modal_module_edit(dataset.objectid);
      }
      if (dataset.object_ == 'theme_entity') {
        show_modal_theme_edit(dataset.objectid);
      }
      if (dataset.object_ == 'activity_entity') {
        show_modal_activity_edit(dataset.objectid);
      }
      if (dataset.object_ == 'assessment_entity') {
        show_modal_assessment_edit(dataset.objectid);
      }
      if (dataset.object_ == 'factdef_entity') {
        show_modal_factdef_edit(dataset.objectid);
      }
    }
    if (dataset.action == 'stream-sync') {
      stream_sync(dataset.objectid);
    }
    if (dataset.action == 'statement-send') {
      statement_send(dataset.url);
    }
    if (dataset.action == 'statement-cancel') {
      statement_cancel(dataset.lrid, dataset.url);
    }
  }

  document.addEventListener('click', event => {
    if (!table.contains(event.target)) {
      return;
    }
    const action_link = event.target.closest('[data-action]');
    if (!action_link) {
      return;
    }
    event.preventDefault();
    call_action(action_link.dataset);
  });
}

export default {
  init,
};
