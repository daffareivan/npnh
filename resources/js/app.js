import './bootstrap';
import Alpine from 'alpinejs';
import ApexCharts from 'apexcharts';

// flatpickr
import flatpickr from 'flatpickr';
import 'flatpickr/dist/flatpickr.min.css';
// FullCalendar
import { Calendar } from '@fullcalendar/core';



window.Alpine = Alpine;
window.ApexCharts = ApexCharts;
window.flatpickr = flatpickr;
window.FullCalendar = Calendar;

window.brandIntro = () => ({
    visible: false,
    exiting: false,
    init() {
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const alreadyShown = sessionStorage.getItem('npnhcreative_intro_shown') === '1';

        if (reducedMotion || alreadyShown) {
            document.documentElement.classList.add('wx-page-ready');
            return;
        }

        this.visible = true;
        document.documentElement.classList.add('wx-intro-running');

        window.setTimeout(() => {
            this.exiting = true;
            document.documentElement.classList.add('wx-page-ready');
        }, 2800);

        window.setTimeout(() => {
            this.visible = false;
            this.exiting = false;
            sessionStorage.setItem('npnhcreative_intro_shown', '1');
            document.documentElement.classList.remove('wx-intro-running');
        }, 3300);
    },
});

window.wxConverter = ({ presets, defaultPresetId, creditBalance = 0, downloadCost = 1, robloxUploadCost = 2 }) => ({
    presets,
    activePresetId: defaultPresetId,
    creditBalance,
    downloadCost,
    robloxUploadCost,
    autoUpload: true,
    dragging: false,
    fileName: '',
    fileSize: '',
    progress: 0,
    statusLabel: 'Waiting',
    uploading: false,
    currentRequest: null,
    audioFile: null,
    result: null,
    robloxUploading: false,
    robloxMessage: '',
    downloadMessage: '',
    downloading: false,
    downloadingAll: false,
    uploadingAllRoblox: false,
    fileActions: {},
    showUpgradeModal: false,
    upgradeMessage: '',
    poller: null,
    timeline: ['Uploading', 'Analyzing Audio', 'Applying Preset', 'Encoding OGG', 'Completed'],
    get activePreset() {
        return this.presets.find((preset) => preset.id === this.activePresetId) || this.presets[0];
    },
    selectPreset(id) {
        this.activePresetId = id;
    },
    handleDrop(event) {
        this.dragging = false;
        this.handleFile(event.dataTransfer.files[0]);
    },
    handleFile(file) {
        if (!file) return;
        this.fileName = file.name;
        this.fileSize = this.formatBytes(file.size);
        this.progress = 0;
        this.statusLabel = 'Ready';
        this.result = null;

        if (this.autoUpload) {
            this.upload(file);
        }
    },
    upload(file) {
        const form = new FormData();
        form.append('file', file);
        form.append('preset_id', this.activePresetId);
        this.uploading = true;
        this.statusLabel = 'Uploading';

        const controller = new AbortController();
        this.currentRequest = controller;

        axios.post('/api/converter/upload', form, {
            signal: controller.signal,
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (event) => {
                this.progress = Math.min(25, Math.round((event.loaded / event.total) * 25));
            },
        }).then((response) => {
            this.audioFile = response.data.data;
            this.progress = this.audioFile.progress;
            this.statusLabel = this.label(this.audioFile.status);
            return axios.post('/api/converter/process', { audio_file_id: this.audioFile.id });
        }).then((response) => {
            this.audioFile = response.data.data;
            this.progress = this.audioFile.progress;
            this.statusLabel = this.label(this.audioFile.status);
            this.pollStatus();
        }).catch((error) => {
            if (error.name !== 'CanceledError') {
                this.progress = 0;
                this.statusLabel = error.response?.data?.message || 'Upload failed';
            }
        }).finally(() => {
            this.uploading = false;
        });
    },
    pollStatus() {
        clearInterval(this.poller);
        this.poller = setInterval(() => {
            axios.get(`/api/converter/status/${this.audioFile.id}`).then((response) => {
                this.audioFile = response.data.data;
                this.progress = this.audioFile.progress;
                this.statusLabel = this.label(this.audioFile.status);

                if (['finished', 'failed'].includes(this.audioFile.status)) {
                    clearInterval(this.poller);
                    this.result = this.audioFile.status === 'finished' ? this.audioFile : null;
                }
            });
        }, 1800);
    },
    cancelUpload() {
        this.currentRequest?.abort();
        this.statusLabel = 'Canceled';
        this.uploading = false;
    },
    deleteResult() {
        if (!this.result) return;
        axios.delete(`/api/converter/history/${this.result.id}`).then(() => this.reset());
    },
    fileState(file) {
        if (!this.fileActions[file.id]) {
            this.fileActions[file.id] = { downloading: false, uploading: false, message: '' };
        }
        return this.fileActions[file.id];
    },
    triggerBlobDownload(blobData, filename) {
        const blobUrl = window.URL.createObjectURL(new Blob([blobData]));
        const link = document.createElement('a');
        link.href = blobUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(blobUrl);
    },
    applyCreditBalance(headerValue) {
        if (headerValue !== undefined) {
            this.creditBalance = Number(headerValue);
        }
    },
    openUpgradeModal(message) {
        this.upgradeMessage = message || '';
        this.showUpgradeModal = true;
    },
    async downloadFile(file) {
        const state = this.fileState(file);
        if (state.downloading || !file.download_url) return;

        if (this.creditBalance < this.downloadCost) {
            this.openUpgradeModal("You don't have enough credits to download this file.");
            return;
        }

        state.downloading = true;
        state.message = '';

        try {
            const response = await axios.get(file.download_url, { responseType: 'blob' });
            this.triggerBlobDownload(response.data, file.file_name);
            this.applyCreditBalance(response.headers['x-credit-balance']);
            file.downloaded_at = new Date().toISOString();
            state.message = 'Downloaded';
        } catch (error) {
            state.message = error.response?.data?.message || 'Download failed';
            if (error.response?.status === 402) {
                this.openUpgradeModal(state.message);
            }
        } finally {
            state.downloading = false;
        }
    },
    async downloadAllFiles() {
        if (!this.result?.download_all_url || this.downloadingAll) return;

        const totalCost = this.downloadCost * (this.result.files?.length || 1);
        if (this.creditBalance < totalCost) {
            this.openUpgradeModal("You don't have enough credits to download all files.");
            return;
        }

        this.downloadingAll = true;

        try {
            const response = await axios.get(this.result.download_all_url, { responseType: 'blob' });
            this.triggerBlobDownload(response.data, this.result.file_name.replace(/\.[^.]+$/, '') + '.zip');
            this.applyCreditBalance(response.headers['x-credit-balance']);
            (this.result.files || []).forEach((file) => { file.downloaded_at = new Date().toISOString(); });
        } catch (error) {
            if (error.response?.status === 402) {
                this.openUpgradeModal(error.response?.data?.message || "You don't have enough credits to download all files.");
            }
        } finally {
            this.downloadingAll = false;
        }
    },
    async uploadFileToRoblox(file) {
        const state = this.fileState(file);
        if (state.uploading) return;

        if (this.creditBalance < this.robloxUploadCost) {
            this.openUpgradeModal("You don't have enough credits to upload this file to Roblox.");
            return;
        }

        state.uploading = true;
        state.message = 'Uploading...';

        try {
            const response = await axios.post(`/api/converter/files/${file.id}/upload-roblox`);
            Object.assign(file, response.data.data);
            this.creditBalance = Math.max(0, this.creditBalance - this.robloxUploadCost);
            state.message = file.upload_status === 'uploaded' ? 'Success' : (file.roblox_error_message || 'Processing');
        } catch (error) {
            state.message = error.response?.data?.message || 'Failed';
            if (error.response?.status === 402) {
                this.openUpgradeModal(state.message);
            }
        } finally {
            state.uploading = false;
        }
    },
    async uploadAllToRoblox() {
        if (!this.result?.files?.length || this.uploadingAllRoblox) return;

        this.uploadingAllRoblox = true;

        for (const file of this.result.files) {
            await this.uploadFileToRoblox(file);
        }

        this.uploadingAllRoblox = false;
    },
    refreshResultStatus() {
        if (!this.result?.id) return;

        axios.get(`/api/converter/status/${this.result.id}`).then((response) => {
            this.result = response.data.data;
            this.creditBalance = this.result.credit_balance ?? this.creditBalance;
            this.robloxMessage = this.result.roblox_asset_id
                ? 'Uploaded to Roblox Creator Hub'
                : (this.result.roblox_error_message || this.robloxMessage || 'Status refreshed');
        });
    },
    reset() {
        clearInterval(this.poller);
        this.fileName = '';
        this.fileSize = '';
        this.progress = 0;
        this.statusLabel = 'Waiting';
        this.audioFile = null;
        this.result = null;
        this.fileActions = {};
    },
    formatDuration(seconds) {
        const total = Math.round(Number(seconds) || 0);
        const minutes = Math.floor(total / 60);
        const secs = total % 60;
        return `${minutes}:${String(secs).padStart(2, '0')}`;
    },
    resultItems() {
        if (!this.result) return [];
        return [
            { label: 'Original File', value: this.result.file_name },
            { label: 'Converted File', value: this.result.file_name.replace(/\.[^.]+$/, '.ogg') },
            { label: 'Duration', value: `${Number(this.result.duration || 0).toFixed(2)}s` },
            { label: 'Output Size', value: this.formatBytes(this.result.output_size || 0) },
            { label: 'Preset', value: `${this.result.speed}x` },
            { label: 'Amplify', value: `${this.result.amplify_db} dB` },
            { label: 'Status', value: this.label(this.result.status) },
        ];
    },
    isStepActive(step) {
        return this.timeline.indexOf(step) <= this.timeline.indexOf(this.statusLabel);
    },
    label(status) {
        const labels = { uploading: 'Uploading', uploaded: 'Uploading', pending: 'Uploading', analyzing: 'Analyzing Audio', converting: 'Applying Preset', encoding: 'Encoding OGG', finished: 'Completed', failed: 'Failed' };
        return labels[status] || status;
    },
    formatBytes(bytes) {
        if (!bytes) return '0 B';
        const units = ['B', 'KB', 'MB', 'GB'];
        const index = Math.floor(Math.log(bytes) / Math.log(1024));
        return `${(bytes / Math.pow(1024, index)).toFixed(2)} ${units[index]}`;
    },
});

Alpine.start();

// Initialize components on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    // Map imports
    if (document.querySelector('#mapOne')) {
        import('./components/map').then(module => module.initMap());
    }

    // Chart imports
    if (document.querySelector('#chartOne')) {
        import('./components/chart/chart-1').then(module => module.initChartOne());
    }
    if (document.querySelector('#chartTwo')) {
        import('./components/chart/chart-2').then(module => module.initChartTwo());
    }
    if (document.querySelector('#chartThree')) {
        import('./components/chart/chart-3').then(module => module.initChartThree());
    }
    if (document.querySelector('#chartSix')) {
        import('./components/chart/chart-6').then(module => module.initChartSix());
    }
    if (document.querySelector('#chartEight')) {
        import('./components/chart/chart-8').then(module => module.initChartEight());
    }
    if (document.querySelector('#chartThirteen')) {
        import('./components/chart/chart-13').then(module => module.initChartThirteen());
    }

    // Calendar init
    if (document.querySelector('#calendar')) {
        import('./components/calendar-init').then(module => module.calendarInit());
    }
});
