/**
 * vim: ts=2 sw=2 sts=2
 * @module local_cdo_unti2035bas/local/modalform_fact_edit
 */

import ModalForm from 'core_form/modalform';
import {get_string} from 'core/str';

class ModalFormFactEdit extends ModalForm {
  constructor(factdefid, actoruntiid, factid = 0) {
    super({
      formClass: 'local_cdo_unti2035bas\\form\\fact_edit',
      args: { factdefid, actoruntiid, factid },
    });
  }

  async show() {
    try {
      return await super.show();
    } finally {
      if (this.config.args.id) {
        this.modal.setTitle(get_string('factedit', 'local_cdo_unti2035bas'));
      } else {
        this.modal.setTitle(get_string('factcreate', 'local_cdo_unti2035bas'));
      }
    }
  }
}

export default ModalFormFactEdit;

