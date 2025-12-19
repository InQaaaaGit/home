/**
 * vim: et ts=2 sts=2 sw=2
 *
 * @module     local_cdo_unti2035bas/local/stream_group_selector
 */
/* eslint-disable jsdoc/require-jsdoc */

import Ajax from 'core/ajax';

async function transport(selector, query, callback) {
    const elSelectGroup = document.querySelector(selector);
    const elForm = elSelectGroup.closest('form.mform');
    const elSelectCourse = elForm.querySelector('select[name=courseid]');
    const courseId = elSelectCourse.value ? Number(elSelectCourse.value) : null;
    if(!courseId) {
        callback([]);
    } else {
        const [res] = await Promise.all(Ajax.call([{
          methodname: 'core_group_get_course_groups',
          args: {
              courseid: courseId,
          },
        }]));
        callback(res);
    }
}

function processResults(selector, results) {
    return results.map(g => ({value: g.id, label: g.name}));
}

export default {
    transport,
    processResults,
};
