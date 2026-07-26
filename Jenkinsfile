pipeline {
    agent any

    environment {
        IMAGE_NAME = "dockerforlearning0213/todo-app"
    }

    stages {

        stage('Checkout') {
            steps {
                echo "Checking out source code..."
                checkout scm
            }
        }

        stage('Project Information') {
            steps {
                sh '''
                    echo "========== Current Directory =========="
                    pwd

                    echo ""
                    echo "========== Project Files =========="
                    ls -la
                '''
            }
        }

        stage('Build Docker Image') {
            steps {
                sh '''
                    echo "========== Building Docker Image =========="

                    docker build -t $IMAGE_NAME:latest .

                    docker images | grep todo-app
                '''
            }
        }

        stage('Push Docker Image') {
            steps {

                withCredentials([
                    usernamePassword(
                        credentialsId: 'dockerhub',
                        usernameVariable: 'DOCKER_USER',
                        passwordVariable: 'DOCKER_PASS'
                    )
                ]) {

                    sh '''
                        echo "========== Docker Login =========="

                        echo "$DOCKER_PASS" | docker login -u "$DOCKER_USER" --password-stdin

                        echo "========== Pushing Image =========="

                        docker push $IMAGE_NAME:latest

                        docker logout
                    '''
                }

            }
        }

        stage('Test Kubernetes Connection') {
            steps {

                withCredentials([
                    file(
                        credentialsId: 'kubeconfig',
                        variable: 'KUBECONFIG'
                    )
                ]) {

                    sh '''
                        echo "========== Kubernetes Test =========="

                        echo "KUBECONFIG = $KUBECONFIG"

                        echo ""
                        echo "========== Current Context =========="
                        kubectl config current-context

                        echo ""
                        echo "========== Cluster Info =========="
                        kubectl cluster-info

                        echo ""
                        echo "========== Nodes =========="
                        kubectl get nodes

                        echo ""
                        echo "========== Pods =========="
                        kubectl get pods -n todo-app

                        echo ""
                        echo "========== Services =========="
                        kubectl get svc -n todo-app
                    '''

                }

            }
        }

    }

    post {

        always {
            echo "Pipeline Finished."
        }

        success {
            echo "Pipeline Successful."
        }

        failure {
            echo "Pipeline Failed."
        }

    }

}
