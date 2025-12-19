import { createRouter, createWebHashHistory } from 'vue-router';
import StudentSurveyList from '../components/StudentSurveyList.vue';
import SurveyList from '../components/SurveyList.vue';
import SurveyFillPage from '../pages/SurveyFillPage.vue';
import TeacherStatsPage from '../pages/TeacherStatsPage.vue';

const routes = [
    {
        path: '/',
        name: 'home',
        redirect: '/surveys',
    },
    {
        path: '/surveys',
        name: 'student-surveys',
        component: StudentSurveyList,
    },
    {
        path: '/admin/surveys',
        name: 'admin-surveys',
        component: SurveyList,
    },
    {
        path: '/survey/:id/fill',
        name: 'survey-fill',
        component: SurveyFillPage,
        props: true,
    },
    {
        path: '/teacher/:teacherId/stats',
        name: 'teacher-stats',
        component: TeacherStatsPage,
        props: true,
    },
];

const router = createRouter({
    history: createWebHashHistory(),
    routes,
});

export default router;

