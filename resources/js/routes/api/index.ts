import users from './users'
import roles from './roles'
import staff from './staff'

const api = {
    users: Object.assign(users, users),
    roles: Object.assign(roles, roles),
    staff: Object.assign(staff, staff),
}

export default api