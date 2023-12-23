import axios from "axios";
import appService from "../../../services/appService";

export const frontendAddReservation = {
    namespaced: true,
    state: {
        reservation : {},
    },
    getters: {
        reservation: function (state) {
            return state.reservation;
        },
    },
    actions: {
        addReservation: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/reservations/add-reservation`,payload).then((res) => {
                    context.commit('reservation', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
    },
    mutations: {
        reservation: function (state, payload) {
            state.reservation = payload;
        },
    },
};
