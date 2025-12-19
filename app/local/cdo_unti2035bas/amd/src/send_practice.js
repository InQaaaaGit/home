// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Send practice assignments module
 *
 * @module     local_cdo_unti2035bas/send_practice
 * @copyright  2024 CDO UNTI 2035 BAS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/* eslint-disable */

define(['jquery', 'core/ajax', 'core/notification', 'core/str'], function($, Ajax, Notification, Str) {
    'use strict';

    var SendPractice = {
        config: null,
        totalUsers: 0,
        processedUsers: 0,
        successfulSends: 0,
        failedSends: 0,
        isProcessing: false,
        abortController: null,

        /**
         * Initialize the module
         * @param {Object} config Configuration object
         */
        init: function(config) {
            this.config = config;
            this.totalUsers = config.validUsersData.length;
            this.bindEvents();
            // Автоматически запускаем отправку
            this.startSending();
        },

        /**
         * Bind event handlers
         */
        bindEvents: function() {
            // Добавляем кнопку отмены
            this.createCancelButton();
        },

        /**
         * Create cancel button
         */
        createCancelButton: function() {
            var cancelBtn = $('<button>')
                .attr('type', 'button')
                .addClass('btn btn-danger')
                .attr('id', 'cancel-send-btn')
                .text('Отменить')
                .hide();
            
            $('#progress-section').prepend(cancelBtn);
            cancelBtn.on('click', this.cancelSending.bind(this));
        },

        /**
         * Start sending practice assignments
         */
        startSending: function() {
            if (this.isProcessing) {
                return;
            }

            this.isProcessing = true;
            this.processedUsers = 0;
            this.successfulSends = 0;
            this.failedSends = 0;

            // Show cancel button
            $('#cancel-send-btn').show();

            // Hide previous results
            $('#errors-section').hide();
            $('#completion-section').hide();

            this.abortController = new AbortController();
            this.sendNextUser(0);
        },

                 /**
          * Cancel sending process
          */
         cancelSending: function() {
             if (this.abortController) {
                 this.abortController.abort();
             }
             
             this.isProcessing = false;
             $('#cancel-send-btn').hide();
             
             Notification.addNotification({
                 message: 'Процесс отправки был отменён',
                 type: 'warning'
             });
         },

        /**
         * Send assignment for next user
         * @param {number} index Current user index
         */
        sendNextUser: function(index) {
            if (!this.isProcessing || index >= this.config.validUsersData.length) {
                this.completeSending();
                return;
            }

            var userData = this.config.validUsersData[index];
            this.markUserAsProcessing(userData.id);

            var request = {
                methodname: 'local_cdo_unti2035bas_send_practice_grade',
                args: {
                    unti_id: userData.unti_id,
                    lrid: this.config.lrid,
                    flow_id: this.config.streamParams.flow_id,
                    parent_course_id: this.config.streamParams.parent_course_id
                }
            };

            Ajax.call([request])[0]
                .done(this.handleUserSuccess.bind(this, userData, index))
                .fail(this.handleUserError.bind(this, userData, index))
                .always(function() {
                    // Small delay for visual effect
                    setTimeout(function() {
                        this.sendNextUser(index + 1);
                    }.bind(this), 200);
                }.bind(this));
        },

        /**
         * Handle successful user send
         * @param {Object} userData User data
         * @param {number} index User index
         * @param {Object} response Server response
         */
        handleUserSuccess: function(userData, index, response) {
            this.processedUsers++;
            this.successfulSends++;
            
            this.markUserAsSuccess(userData.id);
            this.updateProgress();
            this.updateStats();
        },

        /**
         * Handle user send error
         * @param {Object} userData User data
         * @param {number} index User index
         * @param {Object} error Error object
         */
        handleUserError: function(userData, index, error) {
            this.processedUsers++;
            this.failedSends++;
            
            this.markUserAsError(userData.id, error.message || 'Unknown error');
            this.updateProgress();
            this.updateStats();
            
            // Add to failed users list
            this.addFailedUser(userData, error.message || 'Unknown error');
        },

        /**
         * Mark user as processing
         * @param {number} userId User ID
         */
        markUserAsProcessing: function(userId) {
            var userItem = $('[data-userid="' + userId + '"]');
            userItem.removeClass('success error').addClass('processing');
        },

        /**
         * Mark user as successful
         * @param {number} userId User ID
         */
        markUserAsSuccess: function(userId) {
            var userItem = $('[data-userid="' + userId + '"]');
            userItem.removeClass('processing error').addClass('success');
        },

        /**
         * Mark user as error
         * @param {number} userId User ID
         * @param {string} errorMessage Error message
         */
        markUserAsError: function(userId, errorMessage) {
            var userItem = $('[data-userid="' + userId + '"]');
            userItem.removeClass('processing success').addClass('error');
            
            // Update error message in user status
            var statusDiv = userItem.find('.user-status .result-error small');
            if (statusDiv.length) {
                statusDiv.text(errorMessage);
            }
        },

        /**
         * Update progress bar
         */
        updateProgress: function() {
            var percentage = (this.processedUsers / this.totalUsers) * 100;
            $('#progress-fill').css('width', percentage + '%');
            $('#progress-text').text(this.processedUsers + ' / ' + this.totalUsers);
        },

        /**
         * Update statistics
         */
        updateStats: function() {
            $('#successful-count').text(this.successfulSends);
            $('#failed-count').text(this.failedSends);
        },

        /**
         * Add failed user to errors section
         * @param {Object} userData User data
         * @param {string} errorMessage Error message
         */
        addFailedUser: function(userData, errorMessage) {
            if (this.failedSends === 1) {
                $('#errors-section').show();
            }

            var failedUserItem = $('<div>')
                .addClass('user-item')
                .html(
                    '<div>' +
                        '<strong>UNTI ID: ' + this.escapeHtml(userData.unti_id) + '</strong>' +
                        '<br><small>User ID: ' + userData.id + '</small>' +
                    '</div>' +
                    '<div class="result-error">' +
                        '<small>' + this.escapeHtml(errorMessage) + '</small>' +
                    '</div>'
                );

            $('#failed-users-list').append(failedUserItem);
        },

                 /**
          * Complete sending process
          */
         completeSending: function() {
             this.isProcessing = false;
             $('#cancel-send-btn').hide();

            // Show completion notification
            var completionDiv = $('#completion-section');
            completionDiv.empty().show();

            if (this.failedSends === 0) {
                completionDiv.html(
                    '<div class="alert alert-success">' +
                        'Все задания успешно отправлены!' +
                    '</div>'
                );
            } else {
                completionDiv.html(
                    '<div class="alert alert-warning">' +
                        'Процесс завершён с ошибками. Успешно: ' + this.successfulSends + 
                        ', ошибок: ' + this.failedSends +
                    '</div>'
                );
            }
        },

        /**
         * Escape HTML characters
         * @param {string} text Text to escape
         * @return {string} Escaped text
         */
        escapeHtml: function(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }
    };

    return SendPractice;
}); 
