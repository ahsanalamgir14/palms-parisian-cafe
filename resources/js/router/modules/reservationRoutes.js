import ReservationComponent from "../../components/admin/reservations/ReservationComponent";
import OnlineOrderListComponent from "../../components/admin/reservations/OnlineOrderListComponent";

export default [
    {
        path: '/admin/reservations',
        component: ReservationComponent,
        name: 'admin.reservations',
        redirect: {name: 'admin.reservations.list'},
        meta: {
            isFrontend: false,
            auth: true,
            permissionUrl: 'reservations',
            breadcrumb: 'reservations'
        },
        children: [
            {
                path: '',
                component: OnlineOrderListComponent,
                name: 'admin.reservations.list',
                meta: {
                    isFrontend: false,
                    auth: true,
                    permissionUrl: 'reservations',
                    breadcrumb: ''
                },
            }
        ]
    }
]
